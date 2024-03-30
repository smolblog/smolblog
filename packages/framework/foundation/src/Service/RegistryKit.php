<?php

namespace Smolblog\Foundation\Service;

use Psr\Container\ContainerInterface;

trait RegistryKit {
	/**
	 * Store the object factories.
	 *
	 * @var array
	 */
	private $library = [];

	/**
	 * Dependency injection container to retrieve the objects from.
	 *
	 * @var ContainerInterface
	 */
	private ContainerInterface $container;

	/**
	 * Get the key that a given class should be registered under.
	 *
	 * @param string $class Class being registered.
	 * @return string Key to register class under.
	 */
	abstract private function getKeyForClass(string $class): string;

	/**
	 * Configure the Registry
	 *
	 * @param array $configuration List of classes to register.
	 * @return void
	 */
	public function configure(array $configuration): void {
		foreach ($configuration as $class) {
			$this->library[$this->getKeyForClass($class)] = $class;
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
	 * @param string $key Key for class to instantiate and get.
	 * @return mixed Instance of the requested class.
	 */
	public function get(string $key): mixed {
		if (!$this->has($key)) {
			return null;
		}
		return $this->container->get($this->library[$key]);
	}
}
