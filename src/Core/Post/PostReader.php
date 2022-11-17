<?php

namespace Smolblog\Core\Post;

use Smolblog\Framework\Reader;

/**
 * Object to retrieve Posts from the repository.
 */
interface PostReader extends Reader {
	/**
	 * Get the indicated Post from the repository. Should return null if not found.
	 *
	 * @param string|integer $id Unique identifier for the post.
	 * @return Post Post identified by $id; null if it does not exist.
	 */
	public function get(string|int $id): Post;

	/**
	 * Check the given ImportIds and return any that have not been imported.
	 *
	 * @param array $ids ImportIds to check.
	 * @return array ImportIds that have not been imported.
	 */
	public function checkImportIds(array $ids): array;
}
