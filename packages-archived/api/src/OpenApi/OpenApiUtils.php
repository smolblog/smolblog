<?php

namespace Smolblog\Infrastructure\OpenApi;

use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Value\Traits\ArrayType;

/**
 * General utilities for generating OpenAPI specs.
 */
class OpenApiUtils {
	/**
	 * Make an OpenAPI-compatible name from a class. The schema reference for the given class should use this as the
	 * name.
	 *
	 * @param class-string $className Fully-qualified class name.
	 * @return string
	 */
	public static function makeAbbreviatedName(string $className): string {
		return str_replace('\\', '', $className);
	}

	/**
	 * Turn a built-in ArrayType entry into an OpenAPI type.
	 *
	 * @throws CodePathNotSupported If a non-built-in type (or no type) is given.
	 *
	 * @param string $givenType A built-in constant from ArrayType.
	 * @return array
	 */
	public static function builtInArrayTypeSchema(string $givenType): array {
		switch ($givenType) {
			case ArrayType::TYPE_BOOLEAN:
				return ['type' => 'boolean'];
			case ArrayType::TYPE_INTEGER:
				return ['type' => 'integer'];
			case ArrayType::TYPE_FLOAT:
				return ['type' => 'number'];
			case ArrayType::TYPE_STRING:
				return ['type' => 'string'];
		}

		throw new CodePathNotSupported(
			message: "Not a built-in type, given $givenType",
			location: OpenApiUtils::class . '::builtInArrayTypeSchema',
		);
	}
}
