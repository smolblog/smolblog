<?php

namespace Smolblog\Foundation\v2\DomainModel;

use League\ConstructFinder\ConstructFinder;
use ReflectionClass;

trait FileEnumerationKit {
	/**
	 * Get fully-qualified class names from files in the given folders.
	 *
	 * **This will not load the classes.** They must be loaded by other code or configured to correctly autoload.
	 *
	 * @see https://github.com/thephpleague/construct-finder
	 *
	 * @param string $folder                Folder to search. Pass __DIR__ to search the current file's folder.
	 * @param array  $excludingFilePatterns Array of patterns to exclude (e.g. *.view.php).
	 * @param array  $excludingClasses      Array of classes to exclude.
	 * @return class-string[]
	 */
	private static function getClassNamesFromFolder(
		string $folder,
		array $excludingFilePatterns = [],
		array $excludingClasses = [],
	): array {
		$foundClasses = ConstructFinder::locatedIn($folder)->exclude(...$excludingFilePatterns)->findClassNames();
		return array_filter($foundClasses, function ($found) use ($excludingClasses) {
			// If we already know it doesn't exist, filter out.
			if (!class_exists($found) || in_array($found, $excludingClasses)) {
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
