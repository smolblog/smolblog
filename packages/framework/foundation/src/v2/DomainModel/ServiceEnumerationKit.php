<?php

namespace Smolblog\Foundation\v2\DomainModel;

use ReflectionClass;
use ReflectionNamedType;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;

trait ServiceEnumerationKit {
	use ClassEnumerationKit;

	/**
	 * Get the Services defined in this DomainModel and their dependencies.
	 *
	 * @param class-string[] $exclude Classes to exclude from the automapping.
	 * @return array<class-string, array<string, class-string|callable>|string|callable>
	 */
	public static function automapServices(array $exclude = []): array {
		return [];
	}

	/**
	 * Reflect the given service and return its dependency array.
	 *
	 * @throws CodePathNotSupported When service's constructor takes untyped or union/intersection typed arguments.
	 *
	 * @param class-string $service Service to reflect.
	 * @return array<string, mixed>
	 */
	private static function reflectService(string $service): array {
		$reflect = (new ReflectionClass($service))->getConstructor();
		if (!isset($reflect)) {
			return [];
		}

		$params = [];
		foreach ($reflect->getParameters() as $param) {
			$type = $param->getType();
			if (!isset($type) || get_class($type) !== ReflectionNamedType::class || $type->isBuiltin()) {
				throw new CodePathNotSupported(
					message: "{$service} cannot be auto-registered; parameter {$param->getName()} is not a class.",
					location: static::class . '::AUTO_SERVICES',
				);
			}

			$params[$param->getName()] = $type->getName();
		}

		return $params;
	}
}
