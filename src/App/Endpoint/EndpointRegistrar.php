<?php

namespace Smolblog\App\Endpoint;

use Smolblog\App\Registrars\GenericRegistrar;
use Smolblog\App\Registrars\RegistrationException;

/**
 * Template for a class that can take a Smolblog\Core\Endpoint and register it correctly with the external system.
 */
abstract class EndpointRegistrar extends GenericRegistrar {
	/**
	 * Fully-qualified name for the interface to check against.
	 *
	 * @var string
	 */
	protected string $interface = Endpoint::class;

	/**
	 * Register an Endpoint with this Registrar
	 *
	 * @throws RegistrationException Thrown if $class does not implment $this->interface.
	 * @param string $class Fully-qualified class name of an Endpoint class.
	 * @param string $key   Ignored; pulled from config.
	 * @return void
	 */
	public function register(string $class, string $key = null): void {
		$actualKey = $this->processConfig($class::config());
		parent::register(key: $actualKey, class: $class);
	}

	/**
	 * Handle the configuration of the endpoint. Should return the string key used to retrieve the class.
	 *
	 * @param EndpointConfig $config Configuration array from the class.
	 * @return string Key to retrieve the class with.
	 */
	abstract protected function processConfig(EndpointConfig $config): string;
}
