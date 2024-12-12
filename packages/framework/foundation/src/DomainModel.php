<?php

namespace Smolblog\Foundation;

use ReflectionClass;
use ReflectionNamedType;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;

/**
 * Class to centralize services (with dependencies) for a domain.
 */
abstract class DomainModel {
	public const AUTO_SERVICES = [];
	public const SERVICES = [];

	/**
	 * Get services defined in this model and the dependencies they need.
	 *
	 * @return array<class-string, array<string, mixed>>
	 */
	public static function getDependencyMap(): array {
		return [
			...self::autoregisterServices(),
			...static::SERVICES,
		];
	}

	/**
	 * Get services marked for auto-registration, reflect their constructors, and create the dependency map.
	 *
	 * @return array<class-string, array<string, mixed>>
	 */
	private static function autoregisterServices(): array {
		$deps = [];
		foreach (static::AUTO_SERVICES as $service) {
			$deps[$service] = self::reflectService($service);
		}
		return $deps;
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
