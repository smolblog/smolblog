<?php

namespace Smolblog\Core\Channel\Services;

use Cavatappi\Foundation\Registry\Registry;
use Cavatappi\Foundation\Registry\ServiceRegistryKit;
use Cavatappi\Foundation\Service;
use Psr\Container\ContainerInterface;

/**
 * Register ChannelHandler services.
 */
class ChannelHandlerRegistry implements Registry, Service {
	use ServiceRegistryKit;

	/**
	 * This registry handles ChannelHandlers
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return ChannelHandler::class;
	}

	/**
	 * Construct the service
	 *
	 * @param ContainerInterface $container For fetching service instances.
	 */
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	/**
	 * Get the given ChannelHandler from the registry.
	 *
	 * @throws ServiceNotRegistered — When no service is registered with the given key.
	 *
	 * @param string $key Service to fetch.
	 * @return ChannelHandler
	 */
	public function get(string $key): ChannelHandler {
		return $this->getService($key);
	}

	/**
	 * Get the available channel handlers.
	 *
	 * @return array
	 */
	public function availableChannelHandlers(): array {
		return $this->configs;
	}
}
