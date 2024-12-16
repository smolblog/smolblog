<?php

namespace Smolblog\Infrastructure\Endpoint;

use Psr\Container\ContainerInterface;
use Smolblog\Foundation\Service\Registry\Registry;
use Smolblog\Foundation\Service\Registry\RegistryKit;

/**
 * Collect Endpoints and register them.
 */
class EndpointRegistry implements Registry {
	use RegistryKit;

	/**
	 * This registry registers Endpoint services.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return Endpoint::class;
	}

	/**
	 * Construct the service.
	 *
	 * @param ContainerInterface $container Dependency Injection container.
	 */
	public function __construct(private ContainerInterface $container) {
	}

	/**
	 * Debug endpoints.
	 *
	 * @return array
	 */
	public function getEndpoints(): array {
		return $this->configs;
	}
}
