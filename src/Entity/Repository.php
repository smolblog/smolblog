<?php

namespace Smolblog\Core\Entity;

/**
 * An object that can retrieve objects based on identifiers.
 */
interface Repository {
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
	 * @return mixed Object identified by $id; null if it does not exist.
	 */
	public function get(string|int $id): mixed;
}
