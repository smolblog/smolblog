<?php

namespace Smolblog\Core\Media;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Traits\Memoizable;
use Smolblog\Foundation\Value\Traits\MemoizableKit;

/**
 * Search for a media object by its default URL.
 */
class MediaByDefaultUrl extends Query implements Memoizable {
	use MemoizableKit;

	/**
	 * Create the query.
	 *
	 * @param string          $url    URL to search for.
	 * @param Identifier|null $userId Optional user making the search.
	 * @param Identifier|null $siteId Optional site being searched.
	 */
	public function __construct(
		public string $url,
		public readonly ?Identifier $userId = null,
		public readonly ?Identifier $siteId = null,
	) {
	}
}
