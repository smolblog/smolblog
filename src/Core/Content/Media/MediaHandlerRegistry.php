<?php

namespace Smolblog\Core\Content\Media;

use Psr\Container\ContainerInterface;
use Smolblog\Framework\Infrastructure\Registry;
use Smolblog\Framework\Objects\RegistrarKit;

/**
 * Register MediaHandlers.
 */
class MediaHandlerRegistry implements Registry {
	use RegistrarKit {
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
	 * @param ContainerInterface $container     Containter which contains the needed classes.
	 * @param array              $configuration Array of class names to configure the registrar.
	 */
	public function __construct(ContainerInterface $container, array $configuration) {
		$this->container = $container;
		$this->interface = MediaHandler::class;

		foreach ($configuration as $className) {
			$this->library[$className::getHandle()] = $className;
			$this->defaultHandler ??= $className::getHandle();
		}
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
