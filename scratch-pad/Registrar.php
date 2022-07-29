<?php

namespace Smolblog\Core;

/**
 * Trait to handle linking classes to their particular context
 *
 * It is recommended that implmenting classes have `register` and `retrieve` functions
 * with type hinting.
 *
 * ```php
 * final class StringRegistrar {
 *   use Registrar;
 *
 *   public function register(string $value = null, string $withSlug = ''): void {
 *     $this->addToRegistry(object: $value, slug: $withSlug);
 *   }
 *
 *   public function retrieve(string $slug = ''): ?string {
 *     return $this->getFromRegistry(slug: $slug);
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
	private array $registry = [];

	/**
	 * Internal function to add an object to the registry. Will only work if both
	 * $object and $slug are truthy.
	 *
	 * @param mixed  $object Object to store.
	 * @param string $slug   Unique identifier for the object.
	 * @return void
	 */
	protected function addToRegistry(mixed $object, string $slug): void {
		if ($object && $slug) {
			$this->registry[$slug] = $object;
		}
	}

	/**
	 * Get the object from the registry identified by $slug. Returns null if an
	 * object is not found.
	 *
	 * @param string $slug Unique identifier for object.
	 * @return mixed
	 */
	protected function getFromRegistry(string $slug): mixed {
		return $this->registry[$slug] ?? null;
	}
}
