<?php

namespace Smolblog\Framework\Infrastructure;

use Psr\Container\ContainerInterface;
use Smolblog\Framework\Infrastructure\Registry as DeprecatedRegistry;
use Smolblog\Foundation\Service\Registry\Registry;

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
		$services = array_filter(
			array_reduce($models, fn($carry, $item) => array_merge($carry, $item::getDependencyMap()), []),
			fn($srv) => class_exists($srv) || interface_exists($srv),
			ARRAY_FILTER_USE_KEY
		);

		$services = array_merge(
			$services,
			$this->getRegistriesV1($services),
			$this->getRegistryFactories($services),
		);

		return $services;
	}

	private function getRegistriesV1(array $map): array {
		$registryMap = [];
		$registries = array_filter(
			array_keys($map),
			fn($srv) => in_array(DeprecatedRegistry::class, class_implements($srv))
		);

		foreach ($registries as $registry) {
			$interface = $registry::getInterfaceToRegister();
			$config = array_values(array_filter(
				array_keys($map),
				fn($srv) => in_array($interface, class_implements($srv))
			));

			$registryMap[$registry] = [
				...$map[$registry],
				'configuration' => fn() => $config
			];
		}

		return $registryMap;
	}

	private function getRegistryFactories(array $map): array {
		$registryMap = [];
		$registries = array_filter(
			array_keys($map),
			fn($srv) => in_array(Registry::class, class_implements($srv))
		);

		foreach ($registries as $registry) {
			$dependencies = $map[$registry];
			// If we already have a factory in place, move on.
			if (!is_array($dependencies)) {
				continue;
			}

			$interface = $registry::getInterfaceToRegister();
			$servicesToRegister = array_values(array_filter(
				array_keys($map),
				fn($srv) => in_array($interface, class_implements($srv))
			));

			$registryMap[$registry] =
				function (ContainerInterface $container) use ($registry, $dependencies, $servicesToRegister) {
					$service = new $registry(...array_map(fn($srv) => $container->get($srv), $dependencies));
					$service->configure(configuration: $servicesToRegister);
					return $service;
				};
		}

		return $registryMap;
	}
}
