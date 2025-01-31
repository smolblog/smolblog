<?php

// Disable type hint sniff because it doesn't play with the generic type.
// phpcs:disable Squiz.Commenting.FunctionComment.IncorrectTypeHint

namespace Smolblog\Infrastructure\Registries;

use Exception;
use Psr\Container\ContainerInterface;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Exceptions\ServiceNotRegistered;
use Throwable;

/**
 * A basic implementation of a dependency injection container.
 *
 * ServiceRegistry takes a configuration array. The keys are the fully-qualified class names of either service
 * classes or interfaces. The values can be one of three things:
 *
 * - A callable that will instantiate the class. This is useful for low-level classes that take simple configuration
 *   values or interfaces with simple implementations.
 *   Ex: '\PDO' => fn() => new PDO('sqlite:/db.sqlite')
 * - A the fully-qualified class name of an implementing class that is also registered in the container.
 *   Ex: '\Smolblog\MessageBus' => '\Smolblog\Framework\Infrastructure\DefaultMessageBus'
 * - An array of (a) fully-qualified class names that are also registered in the container or (b) callables. The
 *   parameters should be given in order or with appropriate names; passing the spreaded array as the constructor's
 *   arguments should be successful.
 *   Ex: '\oddEvan\Vanity\NameService' => ['\Smolblog\MessageBus', '\Smolblog\Post\Media']
 *   Ex: '\Some\Other\Service' => ['messageBus' => '\Smolblog\MessageBus', 'configArray' => fn() => [1, 2, 3]]
 *
 * ServiceRegistry will register itself so it can be used as a dependency for other services as both ServiceRegistry and
 * the generic ContainerInterface if it is not already present in the given configuration.
 *
 * A supplements array can be provided for calling methods after an object is instantiated. The key is a fully-qualified
 * class name, and the value is an array of arrays with the key being the method to call and the value being the
 * parameters to pass.
 */
class ServiceRegistry implements ContainerInterface {
	/**
	 * Store for the instantiated services.
	 *
	 * @var array<string, mixed>
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
	 *        3. a fully-qualified name of an implementing or substitutionary class
	 *
	 * The supplements array is for calling methods after a service is constructed.
	 *   Key: fully-qualified name of a service class
	 * Value: array of methods to call in the following format:
	 *        Key: name of method
	 *      Value: array of parameters to pass
	 *
	 * @param array<class-string, Callable|array<string|int, class-string|Callable>|string> $configuration Properly
	 *   formatted configuration.
	 * @param array<class-string, array<string, array<mixed>>                               $supplements   Additional
	 *   methods to call after construction.
	 */
	public function __construct(private array $configuration, private array $supplements = []) {
		$this->configuration[self::class] ??= [];
		$this->configuration[ContainerInterface::class] ??= self::class;
		$this->library[self::class] = $this;
	}

	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @template SRV
	 * @param class-string<SRV> $id Identifier of the entry to look for.
	 *
	 * @throws ServiceNotRegistered  No entry was found for **this** identifier.
	 * @throws CodePathNotSupported Error while retrieving the entry.
	 *
	 * @return SRV Entry.
	 */
	public function get(string $id): mixed {
		if (!$this->has($id)) {
			throw new ServiceNotRegistered(service: $id, registry: self::class);
		}

		try {
			$this->library[$id] ??= $this->instantiateService($id);
		} catch (Throwable $e) {
			throw new CodePathNotSupported(
				location: self::class,
				message: "Configuration error for $id: " . $e->getMessage(),
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
		return array_key_exists($id, $this->configuration);
	}

	/**
	 * Use the configuration data to create an instance of the given service.
	 *
	 * @throws Exception Thrown if class_exists returns false for the service.
	 *
	 * @template SRV
	 * @param class-string<SRV> $service Service to instantiate.
	 * @return SRV
	 */
	private function instantiateService(string $service): mixed {
		$config = $this->configuration[$service];
		if (is_callable($config)) {
			// The config is a factory function, so just call it and return the result.
			return call_user_func($config, $this);
		}

		if (is_string($config)) {
			// This is an alias, so we should provide an instance of the implementation.
			return $this->get($config);
		}

		// Get the listed dependencies from the container.
		$args = array_map(
			fn($dependency) => is_callable($dependency) ? call_user_func($dependency, $this) : $this->get($dependency),
			$config
		);

		$instance = new $service(...$args);

		// Call any supplemental methods.
		foreach ($this->supplements[$service] ?? [] as $method => $args) {
			call_user_func_array([$instance, $method], $args);
		}
		return $instance;
	}
}
