<?php

namespace Smolblog\Foundation\v2\DomainModel;

trait ClassEnumerationKit {
	/**
	 * Map of classes and the interfaces they implement.
	 *
	 * @var array<class-string, class-string[]>
	 */
	private static array $classInterfaceMap;

	/**
	 * Create a map of classes and the interfaces they implement to self::$classInterfaceMap.
	 *
	 * @param class-string[] $classNames Classes to parse.
	 * @return void
	 */
	private static function enumerateClasses(array $classNames): void {
		self::$classInterfaceMap ??= [];
		foreach ($classNames as $className) {
			$implements = class_implements($className);
			if ($implements) {
				self::$classInterfaceMap[$className] = $implements;
			}
		}
	}
}
