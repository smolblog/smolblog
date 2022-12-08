<?php

namespace Smolblog\Core\Importer;

use Smolblog\Core\Post\PostReader;

/**
 * Service to filter array of ImportablePosts for posts that have already been imported.
 */
class RemoveAlreadyImported {
	/**
	 * Construct the service
	 *
	 * @param PostReader $postReader PostReader object.
	 */
	public function __construct(
		private PostReader $postReader
	) {
	}

	/**
	 * Filter the given array of ImportablePosts for posts that have already been imported.
	 *
	 * @param ImportablePost[] $posts Full list of posts to check.
	 * @return ImportablePost[] Posts that have not been imported.
	 */
	public function run(array $posts): array {
		$checkedUrls = $this->postReader->checkSyndicatedUrls(array_map(fn($p) => $p->url, $posts));
		$filtered = array_filter($posts, fn($p) => false === array_search($p->url, $checkedUrls));
		return array_values($filtered);
	}
}
