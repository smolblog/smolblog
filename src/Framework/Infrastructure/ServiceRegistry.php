<?php

namespace Smolblog\Framework\Infrastructure;

use Exception;
use Psr\Container\ContainerInterface;
use Smolblog\Framework\Exceptions\ServiceNotFoundException;
use Smolblog\Framework\Exceptions\ServiceRegistryConfigurationException;

/**
 * A basic implementation of a dependency injection container.
 *
 * ServiceRegistry takes a single configuration array. The keys are the fully-qualified class names of either service
 * classes or interfaces. The values can be one of three things:
 *
 * - A callable that will instantiate the class. This is useful for low-level classes that take simple configuration
 *   values or interfaces with simple implementations.
 *   Ex: '\PDO' => fn() => new PDO('sqlite:/db.sqlite')
 * - If the key is an interface, the value should be the fully-qualified class name of an implementing class that is
 *   also registered in the container.
 *   Ex: '\Smolblog\MessageBus' => 'Smolblog\Framework\Infrastructure\DefaultMessageBus'
 * - If the key is a service class, the value should be an array of (a) fully-qualified class names that are also
 *   registered in the container or (b) callables. The parameters should be given in order or with appropriate names;
 *   passing the spreaded array as the constructor's arguments should be successful.
 *   Ex: '\oddEvan\Vanity\NameService' => ['\Smolblog\MessageBus', '\Smolblog\Post\Media']
 *   Ex: '\Some\Other\Service' => ['messageBus' => '\Smolblog\MessageBus', 'configArray' => fn() => [1, 2, 3]]
 *
 * ServiceRegistry will register itself so it can be used as a dependency for other services as both ServiceRegistry and
 * the generic ContainerInterface.
 */
class ServiceRegistry implements ContainerInterface {
	/**
	 * Store for the instantiated services.
	 *
	 * @var array
	 */
	private array $library = [];

	/**
	 * Construct the registrar.
	 *
	 * The configuration is an array with the following format:
	 *   Key: fully-qualified name of a service class or interface
	 * Value: either
	 *        1. a Callable factory that returns a fully instantiated class
	 *        2. an array of named arguments and classes to pass to a constructor
	 *        3. a fully-qualified name of an implementing class (when the key is an interface)
	 *
	 * @param array $configuration Properly-formatted configuration.
	 */
	public function __construct(private array $configuration = []) {
		$this->library = array_fill_keys(keys: array_keys($this->configuration), value: null);
		$this->library[self::class] = $this;
		$this->library[ContainerInterface::class] = $this;
	}

	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @throws ServiceNotFoundException  No entry was found for **this** identifier.
	 * @throws ServiceRegistryConfigurationException Error while retrieving the entry.
	 *
	 * @return mixed Entry.
	 */
	public function get(string $id): mixed {
		if (!$this->has($id)) {
			throw new ServiceNotFoundException(service: $id);
		}

		try {
			$this->library[$id] ??= $this->instantiateService($id);
		} catch (Exception $e) {
			throw new ServiceRegistryConfigurationException(
				service: $id,
				config: $this->configuration[$id],
				previous: $e
			);
		}

		return $this->library[$id];
	}

	/**
	 * Returns true if the container can return an entry for the given identifier.
	 * Returns false otherwise.
	 *
	 * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
	 * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
	 *
	 * @param string $id Identifier of the entry to look for.
	 * @return boolean
	 */
	public function has(string $id): bool {
		return array_key_exists($id, $this->library);
	}

	/**
	 * Use the configuration data to create an instance of the given service.
	 *
	 * @throws ServiceNotImplementedException Thrown if class_exists returns false for the service.
	 *
	 * @param string $service Service to instantiate.
	 * @return mixed
	 */
	private function instantiateService(string $service): mixed {
		$config = $this->configuration[$service];
		if (is_callable($config)) {
			// The config is a factory function, so just call it and return the result.
			return call_user_func($config);
		}

		if (is_string($config)) {
			// This is an alias, so we should provide an instance of the implementation.
			return $this->get($config);
		}

		if (!class_exists($service)) {
			// A class was registered but not actually implemented.
			throw new Exception(message: "Class $service not found.");
		}

		// Get the listed dependencies from the container.
		$args = array_map(
			fn($dependency) => is_callable($dependency) ? call_user_func($dependency) : $this->get($dependency),
			$config
		);

		return new $service(...$args);
	}
}
