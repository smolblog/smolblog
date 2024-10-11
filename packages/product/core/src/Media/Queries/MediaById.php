<?php

namespace Smolblog\Core\Media\Queries;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Traits\Memoizable;
use Smolblog\Foundation\Value\Traits\MemoizableKit;

/**
 * Search for a given media by its ID.
 *
 * Not currently authorized but may in the future.
 */
class MediaById extends Query implements Memoizable {
	use MemoizableKit;

	/**
	 * Construct the query
	 *
	 * @param Identifier      $siteId  ID of the site being queried.
	 * @param Identifier      $mediaId ID of the media being queried.
	 * @param Identifier|null $userId  Optional user making the query.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $mediaId,
		public readonly ?Identifier $userId = null,
	) {
	}
}