<?php

namespace Smolblog\Core\Media\Services;

use Cavatappi\Foundation\Exceptions\ServiceNotRegistered;
use Cavatappi\Foundation\Reflection\TypeRegistry;
use Cavatappi\Foundation\Registry\Registry;
use Cavatappi\Foundation\Registry\ServiceRegistryKit;
use Cavatappi\Foundation\Service;
use Psr\Container\ContainerInterface;
use Smolblog\Core\Media\Entities\MediaExtension;

/**
 * Register available media extensions.
 *
 * I've avoided it as much as I can, but sometimes you just need to have things centrally registered.
 */
class MediaExtensionRegistry implements Registry, Service, TypeRegistry {
	use ServiceRegistryKit;

	/**
	 * This registry handles MediaExtensionService classes.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return MediaExtensionService::class;
	}

	public static function getTypeToRegister(): string {
		return MediaExtension::class;
	}

	/**
	 * Construct the registry.
	 *
	 * @param ContainerInterface $container Dependency Injection container.
	 */
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	/**
	 * List all available media extensions as handle => displayName.
	 *
	 * @return string[]
	 */
	public function availableMediaExtensions(): array {
		return array_map(fn($ct) => $ct->displayName, $this->configs);
	}

	public function keyField(): string {
		return 'type';
	}

	public function findClass(string $id): ?string {
		return $this->configs[$id]?->extensionClass ?? null;
	}

	public function findIdentifier(string $class): ?string {
		return array_find_key($this->configs, fn($config) => $config->extensionClass === $class);
	}

	public function serviceForExtensionObject(MediaExtension $ext): MediaExtensionService {
		$id = $this->findIdentifier(get_class($ext));
		if (!isset($id)) {
			// @codeCoverageIgnoreStart
			throw new ServiceNotRegistered(
				service: get_class($ext),
				registry: self::class,
			);
			// @codeCoverageIgnoreEnd
		}
		return $this->getService($id);
	}
}
