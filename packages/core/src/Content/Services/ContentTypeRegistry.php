<?php

namespace Smolblog\Core\Content\Services;

use Cavatappi\Foundation\Registry\Registry;
use Cavatappi\Foundation\Registry\ServiceRegistryKit;
use Cavatappi\Foundation\Service;
use Psr\Container\ContainerInterface;

/**
 * Register available content types.
 *
 * I've avoided it as much as I can, but sometimes you just need to have things centrally registered.
 */
class ContentTypeRegistry implements Registry, Service {
	use ServiceRegistryKit;

	/**
	 * This registry handles ContentTypeService classes.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return ContentTypeService::class;
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
	 * @param string $type Handle for the content type.
	 * @return string
	 */
	public function typeClassFor(string $type): string {
		return $this->configs[$type]->typeClass;
	}
}
