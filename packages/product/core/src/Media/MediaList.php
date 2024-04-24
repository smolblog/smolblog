<?php

namespace Smolblog\Core\ContentV1\Media;

use Smolblog\Framework\Exceptions\InvalidMessageAttributesException;
use Smolblog\Foundation\Service\Messaging\MemoizableQuery;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Traits\Memoizable;
use Smolblog\Foundation\Value\Traits\MemoizableKit;

/**
 * Get a list of Media objects.
 *
 * All filters are optional; calling this query with no options will return the 30 most recently published media
 * in reverse chronological order.
 *
 * Negative values for `page` and `pageSize` are invalid and will throw exceptions.
 *
 * For nonsensical filters, this query will short-circuit an empty result rather than throw an exception, since a
 * nonsensical option does not necessarily indicate an issue with the program code. These include:
 *
 * - An empty array for `types` (give `null` to ignore the filter)
 */
class MediaList extends Query implements Memoizable {
	use MemoizableKit;

	/**
	 * Construct the query.
	 *
	 * @throws InvalidMessageAttributesException Thown when invalid arguments are provided.
	 *
	 * @param Identifier       $siteId   ID of the site to pull from.
	 * @param integer          $page     Page to show starting from 1; defaults to 1, must be non-negative.
	 * @param integer          $pageSize Number of items per page; defaults to 30, must be positive.
	 * @param Identifier|null  $userId   Optional ID of user making the query to determine media to show.
	 * @param MediaType[]|null $types    Array of media types to show; omit to show all.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly int $page = 1,
		public readonly int $pageSize = 30,
		public readonly ?Identifier $userId = null,
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
			(isset($types) && empty($types))
		) {
			$this->setResults([]);
			$this->stopMessage();
		}
	}
}
