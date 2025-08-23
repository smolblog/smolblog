<?php

namespace Smolblog\Foundation\v2\Module;

use League\ConstructFinder\ConstructFinder;
use ReflectionClass;

trait FileDiscoveryKit {
	/**
	 * Get the list of discoverable classes in this Module.
	 *
	 * @return class-string[]
	 */
	private static function listClasses(): array {
		$dir = new ReflectionClass(static::class)->getFileName();
		if ($dir === false) {
			return [];
		}
		return self::getClassNamesFromFolder(folder: \dirname(\realpath($dir)));
	}

	/**
	 * Get fully-qualified class names from files in the given folders.
	 *
	 * **This will not load the classes.** They must be loaded by other code or configured to correctly autoload.
	 *
	 * @see https://github.com/thephpleague/construct-finder
	 *
	 * @param string $folder                Folder to search. Pass __DIR__ to search the current file's folder.
	 * @param array  $excludingFilePatterns Array of patterns to exclude (e.g. *.view.php).
	 * @return class-string[]
	 */
	private static function getClassNamesFromFolder(
		string $folder,
		array $excludingFilePatterns = [],
	): array {
		$foundClasses = ConstructFinder::locatedIn($folder)->exclude(...$excludingFilePatterns)->findClassNames();
		return \array_filter($foundClasses, static function ($found) {
			// If we already know it doesn't exist, filter out.
			if (!\class_exists($found)) {
				return false;
			}

			$reflection = new ReflectionClass($found);
			// If it's abstract, filter out.
			if ($reflection->isAbstract()) {
				return false;
			}

			return true;
		});
	}
}
