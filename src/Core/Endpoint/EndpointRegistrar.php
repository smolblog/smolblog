<?php

namespace Smolblog\Core\Endpoint;

use Smolblog\Framework\Registrar;
use Smolblog\Core\SmolblogException;

/**
 * Template for a class that can take a Smolblog\Core\Endpoint and register it correctly with the external system.
 */
abstract class EndpointRegistrar implements Registrar {
	/**
	 * Store the object factories.
	 *
	 * @var array
	 */
	protected $library = [];

	/**
	 * Register a class with this Registrar
	 *
	 * @throws RegistrationException Thrown if $class does not implment Runnable.
	 * @param string   $class   Fully-qualified class name of a Runnable class.
	 * @param callable $factory Callable that will return an instance of $class.
	 * @return void
	 */
	public function register(string $class, callable $factory): void {
		if (!in_array(Endpoint::class, class_implements($class))) {
			throw new SmolblogException("Class $class does not implement Registerable.");
		}

		$key = $this->processConfig($class::config());

		$this->library[$key] = $factory;
	}

	/**
	 * Handle the configuration of the class. Should return the string key used to retrieve the class.
	 *
	 * @param EndpointConfig $config Configuration array from the class.
	 * @return string Key to retrieve the class with.
	 */
	abstract protected function processConfig(EndpointConfig $config): string;

	/**
	 * Check if this Registrar has a class registered to the given key.
	 *
	 * @param string $key Key for class to check for.
	 * @return boolean false if $this->get will return null.
	 */
	public function has(string $key): bool {
		return array_key_exists($key, $this->library);
	}

	/**
	 * Get an instance of the class indicated by the given key.
	 *
	 * @param string $key Key for class to instantiate and get.
	 * @return mixed Instance of the requested class.
	 */
	public function get(string $key): mixed {
		if (!array_key_exists($key, $this->library)) {
			return null;
		}
		return call_user_func($this->library[$key]);
	}
}
