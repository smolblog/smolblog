<?php

namespace Smolblog\Core\Content\Services;

use Cavatappi\Foundation\Reflection\TypeRegistry;
use Cavatappi\Foundation\Registry\Registry;
use Cavatappi\Foundation\Registry\ServiceRegistryKit;
use Cavatappi\Foundation\Service;
use Psr\Container\ContainerInterface;
use Smolblog\Core\Content\Entities\ContentExtension;

/**
 * Register available content extensions.
 *
 * I've avoided it as much as I can, but sometimes you just need to have things centrally registered.
 */
class ContentExtensionRegistry implements Registry, Service, TypeRegistry {
	use ServiceRegistryKit;

	/**
	 * This registry handles ContentExtensionService classes.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return ContentExtensionService::class;
	}

	public static function getTypeToRegister(): string {
		return ContentExtension::class;
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
	 * List all available content extensions as handle => displayName.
	 *
	 * @return string[]
	 */
	public function availableContentExtensions(): array {
		return array_map(fn($ct) => $ct->displayName, $this->configs);
	}

	/**
	 * Get the name of the given extension's Extension class.
	 *
	 * @deprecated 0.6 use findClass()
	 *
	 * @param string $extension Handle for the content extension.
	 * @return string
	 */
	public function extensionClassFor(string $extension): string {
		return $this->configs[$extension]->extensionClass;
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

	public function serviceForExtensionObject(ContentExtension $ext): ContentExtensionService {
		$id = $this->findIdentifier(get_class($ext));
		return $this->getService($id);
	}
}
