<?php

namespace Smolblog\Core\Federation;

use Psr\Container\ContainerInterface;
use Smolblog\Foundation\Service\Registry\Registry;
use Smolblog\Foundation\Service\Registry\RegistryKit;

/**
 * Store FollowerProviders for use by the Model.
 */
class FollowerProviderRegistry implements Registry {
	use RegistryKit {
		get as private internalGet;
	}

	/**
	 * Get the interface handled by this Registry.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return FollowerProvider::class;
	}

	/**
	 * Create the service.
	 *
	 * @param ContainerInterface $container     DI container with available services.
	 * @param array              $configuration Array of services to register at construction.
	 */
	public function __construct(ContainerInterface $container, array $configuration) {
		$this->container = $container;
	}

	/**
	 * Retrieve a provider from the registry.
	 *
	 * @param string $key Established slug for the provider.
	 * @return FollowerProvider
	 */
	public function get(string $key): FollowerProvider {
		return $this->internalGet($key);
	}
}
