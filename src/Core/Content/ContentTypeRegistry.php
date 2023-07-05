<?php

namespace Smolblog\Core\Content;

use Smolblog\Framework\Infrastructure\Registry;

/**
 * Register available content types.
 *
 * I've avoided it as much as I can, but sometimes you just need to have things centrally registered.
 */
class ContentTypeRegistry implements Registry {
	/**
	 * This registry handles ContentTypeService classes.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return ContentTypeService::class;
	}

	/**
	 * Store the different content type configurations.
	 *
	 * @var ContentTypeConfiguration[]
	 */
	private array $library = [];

	/**
	 * Construct the registry.
	 *
	 * @param array $configuration List of ContentTypeService classes.
	 */
	public function __construct(array $configuration) {
		foreach ($configuration as $serviceClass) {
			$config = $serviceClass::getConfiguration();
			$this->library[$config->handle] = $config;
		}
	}

	/**
	 * List all available content types as handle => displayName.
	 *
	 * @return string[]
	 */
	public function availableContentTypes(): array {
		return array_map(fn($ct) => $ct->displayName, $this->library);
	}

	/**
	 * Get the name of the given type's Type class.
	 *
	 * @param string $type Handle for the content type.
	 * @return string
	 */
	public function typeClassFor(string $type): string {
		return $this->library[$type]->typeClass;
	}

	/**
	 * Get the name of the given type's single item query.
	 *
	 * @param string $type Handle for the content type.
	 * @return string
	 */
	public function singleItemQueryFor(string $type): string {
		return $this->library[$type]->singleItemQuery;
	}
}
