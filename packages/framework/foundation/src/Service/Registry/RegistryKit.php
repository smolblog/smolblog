<?php

namespace Smolblog\Foundation\Service\Registry;

use Psr\Container\ContainerInterface;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Exceptions\ServiceNotRegistered;
use Smolblog\Foundation\Value\Traits\ServiceConfiguration;

trait RegistryKit {
	/**
	 * Store the object factories.
	 *
	 * @var array
	 */
	private $library = [];

	/**
	 * Store the service configurations.
	 *
	 * @var array
	 */
	private $configs = [];

	/**
	 * Dependency injection container to retrieve the objects from.
	 *
	 * @var ContainerInterface
	 */
	private ContainerInterface $container;

	/**
	 * Configure the Registry
	 *
	 * @throws CodePathNotSupported If the Registry's interface is not Registerable.
	 *
	 * @param array $configuration List of classes to register.
	 * @return void
	 */
	public function configure(array $configuration): void {
		$interface = self::getInterfaceToRegister();
		if (is_a($interface, ConfiguredRegisterable::class, allow_string: true)) {
			$this->configureWithObjects($configuration);
			return;
		}
		if (is_a($interface, Registerable::class, allow_string: true)) {
			$this->configureWithKeys($configuration);
			return;
		}

		throw new CodePathNotSupported(
			message: "$interface must extend Registerable or ConfiguredRegisterable to use RegistryKit.",
			location: 'RegistryKit::configure via ' . self::class,
		);
	}

	/**
	 * Configure the Registry for a Registerable interface.
	 *
	 * @param array $configuration List of classes to register.
	 * @return void
	 */
	private function configureWithKeys(array $configuration): void {
		foreach ($configuration as $class) {
			$this->library[$class::getKey()] = $class;
		}
	}

	/**
	 * Configure the Registry for a ConfiguredRegisterable interface.
	 *
	 * @param array $configuration List of classes to register.
	 * @return void
	 */
	private function configureWithObjects(array $configuration): void {
		foreach ($configuration as $class) {
			$config = $class::getConfiguration();
			$this->configs[$config->getKey()] = $config;
			$this->library[$config->getKey()] = $class;
		}
	}

	/**
	 * Check if this Registry has a class registered to the given key.
	 *
	 * @param string $key Key for class to check for.
	 * @return boolean false if $this->get will return null.
	 */
	public function has(string $key): bool {
		return array_key_exists($key, $this->library) && $this->container->has($this->library[$key]);
	}

	/**
	 * Get an instance of the class indicated by the given key.
	 *
	 * Will throw a ServiceNotRegistered exception if the key does not exist; check with has($key) to avoid this.
	 *
	 * @throws ServiceNotRegistered When no service is registered with the given key.
	 *
	 * @param string $key Key for class to instantiate and get.
	 * @return mixed Instance of the requested class.
	 */
	public function getService(string $key): mixed {
		if (!$this->has($key)) {
			throw new ServiceNotRegistered(service: $key, registry: static::class);
		}
		return $this->container->get($this->library[$key]);
	}

	/**
	 * Get the configuration for the service indicated by the given key.
	 *
	 * @param string $key Key for the service configuration.
	 * @return ServiceConfiguration|null Configuration; null if it does not exist.
	 */
	public function getConfig(string $key): ?ServiceConfiguration {
		return $this->configs[$key] ?? null;
	}
}
