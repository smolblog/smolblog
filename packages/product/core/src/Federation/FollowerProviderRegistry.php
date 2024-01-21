<?php

namespace Smolblog\Core\Federation;

use Psr\Container\ContainerInterface;
use Smolblog\Framework\Infrastructure\Registry;
use Smolblog\Framework\Objects\RegistrarKit;

/**
 * Store FollowerProviders for use by the Model.
 */
class FollowerProviderRegistry implements Registry {
	use RegistrarKit {
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
		$this->interface = self::getInterfaceToRegister();

		foreach ($configuration as $provider) {
			$this->register($provider::getSlug(), $provider);
		}
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
