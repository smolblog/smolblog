<?php

namespace Smoolblog\Core\Importer;

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
	public function __invoke(array $posts): array {
		$checkedIds = $this->postReader->checkImportIds(array_map(fn($p) => $p->importId, $posts));
		return array_filter($posts, fn($p) => false !== array_search($p->importId, $checkedIds));
	}
}
