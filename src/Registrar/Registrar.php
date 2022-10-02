<?php

namespace Smolblog\Core\Registrar;

/**
 * Class to handle registering a type.
 */
abstract class Registrar {
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
		if (!in_array(Registerable::class, class_implements($class))) {
			throw new RegistrationException("Class $class does not implement Registerable.");
		}

		$key = $this->processConfig($class::config());

		$this->library[$key] = $factory;
	}

	/**
	 * Handle the configuration of the class. Should return the string key used to retrieve the class.
	 *
	 * @param mixed $config Configuration array from the class.
	 * @return string Key to retrieve the class with.
	 */
	abstract protected function processConfig(mixed $config): string;

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
