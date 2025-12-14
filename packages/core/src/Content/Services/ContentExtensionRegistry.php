<?php

namespace Smolblog\Core\Content\Services;

use Psr\Container\ContainerInterface;
use Smolblog\Foundation\Service\Registry\Registry;
use Smolblog\Foundation\Service\Registry\RegistryKit;

/**
 * Register available content extensions.
 *
 * I've avoided it as much as I can, but sometimes you just need to have things centrally registered.
 */
class ContentExtensionRegistry implements Registry {
	use RegistryKit;

	/**
	 * This registry handles ContentExtensionService classes.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return ContentExtensionService::class;
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
	 * @param string $extension Handle for the content extension.
	 * @return string
	 */
	public function extensionClassFor(string $extension): string {
		return $this->configs[$extension]->extensionClass;
	}
}
