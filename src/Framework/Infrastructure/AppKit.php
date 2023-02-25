<?php

namespace Smolblog\Framework\Infrastructure;

/**
 * Useful functions for building an App from DomainModels.
 */
trait AppKit {
	/**
	 * Build a ServiceRegistry with the Default Model and any other supplied DomainModels.
	 *
	 * @param array $models Class names of additional DomainModels to load.
	 * @return ServiceRegistry
	 */
	private function buildDefaultContainer(array $models = []): ServiceRegistry {
		return new ServiceRegistry($this->buildDependencyMap([
			DefaultModel::class,
			...$models,
		]));
	}

	/**
	 * Build the dependency map for the given DomainModels.
	 *
	 * @param array $models DomainModel class names.
	 * @return array
	 */
	private function buildDependencyMap(array $models): array {
		$services = array_reduce($models, fn($carry, $item) => array_merge($carry, $item::SERVICES), []);

		$registries = array_filter(
			array_keys($services),
			fn($srv) => in_array(Registry::class, class_implements($srv))
		);

		foreach ($registries as $registry) {
			$interface = $registry::getInterfaceToRegister();
			$services[$registry]['configuration'] = array_values(array_filter(
				array_keys($services),
				fn($srv) => in_array($interface, class_implements($srv))
			));
		}

		return $services;
	}
}
