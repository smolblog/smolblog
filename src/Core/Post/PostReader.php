<?php

namespace Smolblog\Core\Post;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Reader;

/**
 * Object to retrieve Posts from the repository.
 */
interface PostReader extends Reader {
	/**
	 * Get the indicated Post from the repository. Should return null if not found.
	 *
	 * @param Identifier $id Unique identifier for the post.
	 * @return Post Post identified by $id; null if it does not exist.
	 */
	public function get(Identifier $id): Post;

	/**
	 * Check the given URLs and return any that match a post's syndication URLs.
	 *
	 * @param array $urls Urls to check.
	 * @return array URLs that have matching posts.
	 */
	public function checkSyndicatedUrls(array $urls): array;

	/**
	 * Find any posts matching the given parameters.
	 *
	 * Parameters are considered to be joined with AND; posts returned will match ALL given parameters.
	 *
	 * @param mixed ...$params Parameters to search by.
	 * @return Post[]
	 */
	public function findBy(mixed ...$params): array;
}
