<?php

namespace Smolblog\Core\Content\Services;

use Cavatappi\Foundation\Reflection\TypeRegistry;
use Cavatappi\Foundation\Registry\Registry;
use Cavatappi\Foundation\Registry\ServiceRegistryKit;
use Cavatappi\Foundation\Service;
use Psr\Container\ContainerInterface;
use Smolblog\Core\Content\Entities\ContentType;

/**
 * Register available content types.
 *
 * I've avoided it as much as I can, but sometimes you just need to have things centrally registered.
 */
class ContentTypeRegistry implements Registry, Service, TypeRegistry {
	use ServiceRegistryKit;

	/**
	 * This registry handles ContentTypeService classes.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return ContentTypeService::class;
	}

	public static function getTypeToRegister(): string {
		return ContentType::class;
	}

	/**
	 * Construct the registry.
	 *
	 * @param ContainerInterface $container Dependency Injection container.
	 */
	public function __construct(private ContainerInterface $container) {
		$this->container = $container;
	}

	/**
	 * List all available content types as handle => displayName.
	 *
	 * @return string[]
	 */
	public function availableContentTypes(): array {
		return array_map(fn($ct) => $ct->displayName, $this->configs);
	}

	/**
	 * Get the name of the given type's Type class.
	 *
	 * @deprecated 0.6 Use findClass instead.
	 *
	 * @param string $type Handle for the content type.
	 * @return string
	 */
	public function typeClassFor(string $type): string {
		return $this->configs[$type]->typeClass;
	}

	public function keyField(): string {
		return 'type';
	}

	public function findClass(string $id): ?string {
		return $this->configs[$id]?->typeClass ?? null;
	}

	public function findIdentifier(string $class): ?string {
		return array_find_key($this->configs, fn($config) => $config->typeClass === $class);
	}
}
