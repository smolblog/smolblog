<?php

namespace Smolblog\App\Registrars;

use Psr\Container\ContainerInterface;
use Smolblog\Framework\Registrar;

/**
 * Base behavior for Registrar implementations
 */
abstract class GenericRegistrar implements Registrar {
	/**
	 * Store the object factories.
	 *
	 * @var array
	 */
	protected $library = [];

	/**
	 * Fully-qualified name for the interface to check against.
	 *
	 * @var string
	 */
	protected string $interface;

	/**
	 * Construct the registrar
	 *
	 * @param ContainerInterface $container PSR-11 container to retrieve instances from.
	 */
	public function __construct(
		protected ContainerInterface $container
	) {
	}

	/**
	 * Register a class with this Registrar
	 *
	 * @throws RegistrationException Thrown if $class does not implment $this->interface.
	 * @param string $key   Known key to reference this class by.
	 * @param string $class Fully-qualified class name of a Runnable class.
	 * @return void
	 */
	public function register(string $key, string $class): void {
		if (!in_array($this->interface, class_implements($class))) {
			throw new RegistrationException("Class $class does not implement $this->interface.");
		}

		$this->library[$key] = $class;
	}

	/**
	 * Check if this Registrar has a class registered to the given key.
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
