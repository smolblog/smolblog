<?php

namespace Smolblog\Core\Post;

use Smolblog\Core\Entity\Reader;

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
}
