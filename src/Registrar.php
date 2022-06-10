<?php

namespace Smolblog\Core;

/**
 * Trait to handle linking classes to their particular context
 *
 * Classes with this trait are required to implement `register` and `retrieve`
 * functions as their public interface. An example:
 *
 * ```php
 * final class StringRegistrar {
 *   use Registrar;
 *
 *   public static function register(string $value = null, string $withSlug = ''): void {
 *     static::addToRegistry(object: $value, slug: $withSlug);
 *   }
 *
 *   public static function retrieve(string $slug = ''): ?string {
 *     return static::getFromRegistry(slug: $slug);
 *   }
 * }
 * ```
 */
trait Registrar {
	private static array $registry = [];

	protected static function addToRegistry(mixed $object = null, string $slug = ''): void {
		if ($object && $slug) {
			static::$registry[$slug] = $object;
		}
	}

	protected static function getFromRegistry(string $slug): mixed {
		return static::$registry[$slug] ?? null;
	}

	abstract public static function register();

	abstract public static function retrieve();
}
