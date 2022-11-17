<?php

namespace Smolblog\Framework;

/**
 * An object that can retrieve objects from a repository based on identifiers.
 * Named consistently with Container and Registrar, but not related.
 */
interface Reader {
	/**
	 * Check the repository for the object identified by $id.
	 *
	 * @param string|integer $id Unique identifier for the object.
	 * @return boolean True if the repository contains an object with the given $id.
	 */
	public function has(string|int $id): bool;

	/**
	 * Get the indicated object from the repository. Should return null if not found.
	 *
	 * @param string|integer $id Unique identifier for the object.
	 * @return Entity Object identified by $id; null if it does not exist.
	 */
	public function get(string|int $id): Entity;
}
