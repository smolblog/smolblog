<?php

namespace Smolblog\Core\ContentV1\Media;

use Smolblog\Foundation\Service\Messaging\MemoizableQuery;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Search for a media object by its default URL.
 */
readonly class MediaByDefaultUrl extends Query implements Memoizable {
	use MemoizableKit;
	/**
	 * Create the query.
	 *
	 * @param string          $url    URL to search for.
	 * @param Identifier|null $userId Optional user making the search.
	 * @param Identifier|null $siteId Optional site being searched.
	 */
	public function __construct(
		public readonly string $url,
		public readonly ?Identifier $userId = null,
		public readonly ?Identifier $siteId = null,
	) {
	}
}
