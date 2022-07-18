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
	/**
	 * Array to store the registered objects for later retrieval.
	 *
	 * @var array
	 */
	private static array $registry = [];

	/**
	 * Internal function to add an object to the registry. Will only work if both
	 * $object and $slug are truthy.
	 *
	 * @param mixed  $object Object to store.
	 * @param string $slug   Unique identifier for the object.
	 * @return void
	 */
	protected static function addToRegistry(mixed $object = null, string $slug = ''): void {
		if ($object && $slug) {
			static::$registry[$slug] = $object;
		}
	}

	/**
	 * Get the object from the registry identified by $slug. Returns null if an
	 * object is not found.
	 *
	 * @param string $slug Unique identifier for object.
	 * @return mixed
	 */
	protected static function getFromRegistry(string $slug): mixed {
		return static::$registry[$slug] ?? null;
	}

	/**
	 * Required function for implementing classes to allow registration.
	 *
	 * @return void
	 */
	abstract public static function register();

	/**
	 * Required function for implementing classes to allow retrieval.
	 *
	 * @return void
	 */
	abstract public static function retrieve();
}
