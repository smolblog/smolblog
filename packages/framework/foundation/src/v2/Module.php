<?php

namespace Smolblog\Foundation\v2;

/**
 * Class to centralize information about a domain/package.
 */
interface Module {
	/**
	 * Array of class name keys with the interfaces they implement.
	 *
	 * @return array<class-string, class-string[]>
	 */
	public static function discoverableClasses(): array;

	/**
	 * Get the Services to be registered in this Model and their dependencies.
	 *
	 * @return array<class-string, array<string, class-string|callable>|string|callable>
	 */
	public static function serviceDependencyMap(): array;
}
