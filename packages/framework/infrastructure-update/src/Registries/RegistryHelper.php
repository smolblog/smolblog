<?php

namespace Smolblog\Infrastructure\Registries;

use Smolblog\Foundation\Service\Registry\Registry;

/**
 * Helper tool to get a configuration array for a registry.
 */
class RegistryHelper {
	/**
	 * Get the configuration for each Registry in the array.
	 *
	 * @param class-string[] $services List of available Services to search.
	 * @return array<class-string, class-string[]>
	 */
	public static function getRegistryConfigs(array $services): array {
		$map = [];
		$registries = [];
		foreach ($services as $service) {
			if (!class_exists($service)) { // @codeCoverageIgnore
				continue;
			}

			$implements = class_implements($service);
			if (!$implements) {
				// This exists for static analysis; class_implements should always be an array since we already checked
				// if the class exists. This line should never be called.
				continue; // @codeCoverageIgnore
			}

			$map[$service] = $implements;
			if (in_array(Registry::class, $implements, strict: true)) {
				$registries[$service] = $service;
			}
		}

		return array_map(
			fn($reg) => self::getImplementingClassesForRegistry($map, $reg),
			$registries,
		);
	}

	/**
	 * Filter the map of implemented interfaces for the given service.
	 *
	 * @param array<class-string, class-string[]> $map      Map of classes and the interfaces they implement.
	 * @param class-string                        $registry Registry being filtered for.
	 * @return class-string[]
	 */
	private static function getImplementingClassesForRegistry(array $map, string $registry): array {
		$search = $registry::getInterfaceToRegister();
		$filtered = array_filter(
			$map,
			fn($imp) => in_array($search, $imp, strict: true)
		);

		return array_keys($filtered);
	}
}
