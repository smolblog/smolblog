<?php

namespace Smolblog\Foundation\v2\Registry;

/**
 * Functions for adding classes to Registries.
 */
trait RegistryConfiguratorKit {
	/**
	 * Get the configuration for each Registry in the array.
	 *
	 * @param array<class-string, class-string[]> $discoveredClasses List of available classes and their interfaces.
	 * @return array<class-string, class-string[]> List of Registries and their registered classes.
	 */
	protected static function getRegistryConfigs(array $discoveredClasses): array {
		return \array_map(
			fn($reg) => self::getImplementingClassesForRegistry($discoveredClasses, $reg),
			\array_keys(\array_filter($discoveredClasses, fn($imp) => \in_array(Registry::class, $imp))),
		);
	}

	/**
	 * Filter the map of implemented interfaces for the given service.
	 *
	 * @param array<class-string, class-string[]> $map      Map of classes and the interfaces they implement.
	 * @param class-string                        $registry Registry being filtered for.
	 * @return class-string[] Classes to be registerd by $registry.
	 */
	protected static function getImplementingClassesForRegistry(array $map, string $registry): array {
		$search = $registry::getInterfaceToRegister();
		$filtered = \array_filter(
			$map,
			fn($imp) => \in_array($search, $imp, strict: true)
		);

		return \array_keys($filtered);
	}
}
