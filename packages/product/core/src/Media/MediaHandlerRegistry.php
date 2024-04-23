<?php

namespace Smolblog\Core\ContentV1\Media;

use Psr\Container\ContainerInterface;
use Smolblog\Foundation\Service\Registry\Registry;
use Smolblog\Foundation\Service\Registry\RegistryKit;

/**
 * Register MediaHandlers.
 */
class MediaHandlerRegistry implements Registry {
	use RegistryKit {
		get as private baseGet;
	}

	/**
	 * Store the default media handler.
	 *
	 * @var string
	 */
	private string $defaultHandler;

	/**
	 * This registry handles MediaHandler services.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return MediaHandler::class;
	}

	/**
	 * Construct the Registrar with a DI container
	 *
	 * @param ContainerInterface $container Containter which contains the needed classes.
	 */
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	/**
	 * Get the given MediaHandler from the Registrar
	 *
	 * @param string $key Key for the MediaHandler.
	 * @return MediaHandler
	 */
	public function get(?string $key = null): MediaHandler {
		return $this->baseGet($key ?? $this->defaultHandler);
	}
}
