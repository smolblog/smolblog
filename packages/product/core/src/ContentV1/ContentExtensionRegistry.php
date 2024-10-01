<?php

namespace Smolblog\Core\Content;

use Smolblog\Framework\Infrastructure\Registry;

/**
 * Register available content extensions.
 *
 * I've avoided it as much as I can, but sometimes you just need to have things centrally registered.
 */
class ContentExtensionRegistry implements Registry {
	/**
	 * This registry handles ContentExtensionService classes.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return ContentExtensionService::class;
	}

	/**
	 * Store the different content extension configurations.
	 *
	 * @var ContentExtensionConfiguration[]
	 */
	private array $library = [];

	/**
	 * Construct the registry.
	 *
	 * @param array $configuration List of ContentExtensionService classes.
	 */
	public function __construct(array $configuration) {
		foreach ($configuration as $serviceClass) {
			$config = $serviceClass::getConfiguration();
			$this->library[$config->handle] = $config;
		}
	}

	/**
	 * List all available content extensions as handle => displayName.
	 *
	 * @return string[]
	 */
	public function availableContentExtensions(): array {
		return array_map(fn($ct) => $ct->displayName, $this->library);
	}

	/**
	 * Get the name of the given extension's Extension class.
	 *
	 * @param string $extension Handle for the content extension.
	 * @return string
	 */
	public function extensionClassFor(string $extension): string {
		return $this->library[$extension]->extensionClass;
	}
}
