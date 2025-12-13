<?php

namespace Smolblog\Foundation\v2\Utilities;

/**
 * Utility functions for working with strings.
 *
 * Declared as a class so it will only be autoloaded if necessary.
 */
final class StringUtils {
	/**
	 * Take a camel-case string and turn it into a title case string.
	 *
	 * For example: `socialAccountName` becomes `Social Account Name`.
	 *
	 * @param string $camelCase String to convert.
	 * @return string
	 */
	public static function camelToTitle(string $camelCase): string {
		// Via https://stackoverflow.com/a/42665007.
		return \ucwords(\implode(' ', \preg_split('/(?=[A-Z])/', $camelCase) ?: []));
	}

	/**
	 * Remove the namespace from a fully-qualified class name.
	 *
	 * @param class-string $className Fully qualified class name.
	 * @return string
	 */
	public static function dequalifyClassName(string $className): string {
		$path = \explode('\\', $className);
		return \array_pop($path) ?? $className;
	}
}
