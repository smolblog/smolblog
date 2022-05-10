<?php

namespace Smolblog\Core;

use Smolblog\Exceptions\EnvironmentException;

/**
 * A singleton class for handling interactions between the Smolblog libraries
 * and the broader platform/framework/environment it is running on.
 *
 * This class should be extended with a class built to handle implementation-
 * specific functionality. An instance of that class should then be passed
 * to the static `bootstrap` function to make it available to the core
 * libraries.
 *
 * When adding new instance methods, provide a default if it makes logical
 * sense. If a default value cannot be inferred or would otherwise be
 * illogical, throw an exception. Avoid abstract functions as not all
 * environments will make use of all features.
 */
abstract class Environment {
	// Static methods.

	/**
	 * Store the current Environment.
	 *
	 * @var Environment
	 */
	private static Environment $singleton;

	/**
	 * Load the given Environment as the current Environment.
	 *
	 * @param Environment $withEnvironment Environment for this implementation.
	 * @throws EnvironmentException When this function is called multiple times.
	 * @return void
	 */
	public static function bootstrap(Environment $withEnvironment): void {
		if (self::$singleton) {
			throw new EnvironmentException(
				environment: self::$singleton,
				message: 'Smolblog\\Core\\Environment::bootstrap should only be called ONCE.'
			);
		}

		self::$singleton = $withEnvironment;
	}

	/**
	 * Get the environment instance.
	 *
	 * @throws EnvironmentException When `bootstrap` has not been called.
	 * @return Environment Environment for this implementation.
	 */
	public static function get(): Environment {
		if (!self::$singleton) {
			throw new EnvironmentException(
				environment: self::$singleton,
				message: 'Smolblog environment has not been bootstrapped yet.'
			);
		}

		return self::$singleton;
	}

	// Instance methods.

	/**
	 * Register the given Endpoint with the system to allow it to receive
	 * requests.
	 *
	 * @param Endpoint $endpoint Endpoint to register.
	 * @throws EnvironmentException When this function is called without being implemented.
	 * @return void
	 */
	public function registerEndpoint(Endpoint $endpoint): void {
		throw new EnvironmentException(
			environment: self::$singleton,
			message: 'registerEndpoint was called without being implemented.'
		);
	}
}
