<?php

namespace Smolblog\Core\Media\Services;

use Psr\Container\ContainerInterface;
use Smolblog\Foundation\Exceptions\ServiceNotRegistered;
use Smolblog\Foundation\Service\Registry\Registry;
use Smolblog\Foundation\Service\Registry\RegistryKit;

/**
 * Register MediaHandlers.
 */
class MediaHandlerRegistry implements Registry {
	use RegistryKit;

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
	 * @param ContainerInterface $container         Containter which contains the needed classes.
	 * @param string|null        $defaultHandlerKey Key of MediaHandler to use by default.
	 */
	public function __construct(ContainerInterface $container, private ?string $defaultHandlerKey = null) {
		$this->container = $container;
	}

	/**
	 * Get the given MediaHandler from the Registrar
	 *
	 * @throws ServiceNotRegistered When no service is registered with the given key.
	 *
	 * @param string|null $key Key for the MediaHandler.
	 * @return MediaHandler
	 */
	public function get(?string $key = null): MediaHandler {
		$keyToUse = $key ?? $this->defaultHandlerKey ?? \array_key_first($this->library);
		if (!is_string($keyToUse)) {
			throw new ServiceNotRegistered(
				service: 'default',
				registry: self::class,
				message: 'No default MediaHandler set.'
			);
		}
		return $this->getService($keyToUse);
	}
}
