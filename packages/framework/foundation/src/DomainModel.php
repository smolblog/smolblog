<?php

namespace Smolblog\Foundation;

use League\ConstructFinder\ConstructFinder;
use ReflectionClass;
use ReflectionNamedType;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;

/**
 * Class to centralize services (with dependencies) for a domain.
 */
abstract class DomainModel {
	protected const DISCOVER = false;
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
	 * Discover any classes that implement Service within the subclass file's folder.
	 *
	 * @return array
	 */
	private static function discoverServices(): array {
		$dir = static::DISCOVER;

		// If ::DISCOVER is boolean true, get the directory of the file.
		if ($dir === true) {
			$dir = new ReflectionClass(static::class)->getFileName();
			if ($dir !== false) {
				$dir = dirname(realpath($dir));
			}
		}
		// If ::DISCOVER is false or an empty string, do not discover.
		if (!$dir || !is_string($dir)) {
			return [];
		}

		$foundClasses = ConstructFinder::locatedIn($dir)->findClassNames();
		return array_filter($foundClasses, static function ($found) {
			// If we already know it doesn't exist or isn't a Service, filter out.
			if (!class_exists($found) || !is_a($found, Service::class, true)) {
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

	/**
	 * Get services marked for auto-registration, reflect their constructors, and create the dependency map.
	 *
	 * @return array<class-string, array<string, mixed>>
	 */
	private static function autoregisterServices(): array {
		$autoreg = [
			...self::discoverServices(),
			...static::AUTO_SERVICES,
		];

		$deps = [];
		foreach ($autoreg as $service) {
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
