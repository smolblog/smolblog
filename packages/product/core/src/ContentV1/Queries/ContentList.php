<?php

namespace Smolblog\Core\ContentV1\Queries;

use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Framework\Exceptions\InvalidMessageAttributesException;
use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get a list of GenericContent objects.
 *
 * All filters are optional; calling this query with no options will return the 30 most recently published content items
 * in reverse chronological order.
 *
 * Negative values for `page` and `pageSize` are invalid and will throw exceptions.
 *
 * For nonsensical filters, this query will short-circuit an empty result rather than throw an exception, since a
 * nonsensical option does not necessarily indicate an issue with the program code. These include:
 *
 * - An empty array for `visibility` or `types` (give `null` to ignore the filter)
 * - Not including Published in `visibility` on an unauthenticated query (`userId` is null)
 */
class ContentList extends MemoizableQuery {
	/**
	 * Upon completion, will get the total number of content items for the given query.
	 *
	 * @var integer
	 */
	public int $count = 0;

	/**
	 * Construct the query.
	 *
	 * @throws InvalidMessageAttributesException Thown when invalid arguments are provided.
	 *
	 * @param Identifier      $siteId     ID of the site to pull from.
	 * @param integer         $page       Page to show starting from 1; defaults to 1, must be non-negative.
	 * @param integer         $pageSize   Number of items per page; defaults to 30, must be positive.
	 * @param Identifier|null $userId     Optional ID of user making the query to determine draft/hidden posts to show.
	 * @param array|null      $visibility Array of ContentVisibility types to show; omit to show all.
	 * @param array|null      $types      Array of content types to show; omit to show all.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly int $page = 1,
		public readonly int $pageSize = 30,
		public readonly ?Identifier $userId = null,
		public readonly ?array $visibility = null,
		public readonly ?array $types = null,
	) {
		// Check for invalid values; these indicate a programming error.
		if (
			$page <= 0 ||
			$pageSize <= 0
		) {
			throw new InvalidMessageAttributesException("Invalid filters given");
		}

		// Check for nonsensical values; these could be user error.
		if (
			(isset($visibility) && empty($visibility)) ||
			(!isset($userId) && isset($visibility) && !in_array(ContentVisibility::Published, $visibility)) ||
			(isset($types) && empty($types))
		) {
			$this->setResults([]);
			$this->stopMessage();
		}
	}
}
