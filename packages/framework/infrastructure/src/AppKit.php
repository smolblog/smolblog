<?php

namespace Smolblog\Infrastructure;

use Smolblog\Infrastructure\Registries\RegistryHelper;
use Smolblog\Infrastructure\Registries\ServiceRegistry;

/**
 * Useful functions for building an App from DomainModels.
 */
trait AppKit {
	/**
	 * Build a ServiceRegistry with the Default Model and any other supplied DomainModels. Registers services and
	 * provides Registry configurations.
	 *
	 * @param array $models Class names of additional DomainModels to load.
	 * @return ServiceRegistry
	 */
	private function buildContainerFromModels(array $models = []): ServiceRegistry {
		$map = $this->buildDependencyMap($models);

		return new ServiceRegistry(
			configuration: $map,
			supplements: $this->buildSupplementsForRegistries(array_keys($map)),
		);
	}

	/**
	 * Build the dependency map for the given DomainModels.
	 *
	 * @param array $models DomainModel class names.
	 * @return array
	 */
	private function buildDependencyMap(array $models): array {
		return array_reduce(
			array_map(
				fn($model) => $model::getDependencyMap(),
				$models
			),
			fn($carry, $item) => array_merge($carry, $item),
			[]
		);
	}

	/**
	 * Translates the configs from RegistryHelper into the format needed by ServiceRegistry.
	 *
	 * @param array $services List of classes to check.
	 * @return array Supplements array for ServiceRegistry
	 */
	private function buildSupplementsForRegistries(array $services): array {
		return array_map(
			fn($conf) => ['configure' => ['configuration' => $conf]],
			RegistryHelper::getRegistryConfigs($services),
		);
	}
}
