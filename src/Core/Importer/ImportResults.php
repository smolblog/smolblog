<?php

namespace Smolblog\Core\Importer;

use Smolblog\Framework\Command;
use Smolblog\Core\Post\Post;
use Smolblog\Framework\Value;

/**
 * The results of an import process.
 */
readonly class ImportResults extends Value {
	/**
	 * Construct the object.
	 *
	 * @param Post[]       $posts           Array of Post objects to save.
	 * @param Command|null $nextPageCommand Optional command to call to fetch the next page of results.
	 */
	public function __construct(
		public readonly array $posts,
		public readonly ?Command $nextPageCommand = null
	) {
	}
}
