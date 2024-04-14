<?php

namespace Smolblog\Core\Federation;

use Smolblog\Foundation\Service\Messaging\MemoizableQuery;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get the array of followers for the given site.
 */
readonly class GetFollowersForSiteByProvider extends Query implements Memoizable {
	use MemoizableKit;
	/**
	 * Construct the query.
	 *
	 * @param Identifier $siteId Site to query.
	 */
	public function __construct(
		public readonly Identifier $siteId,
	) {
	}

	/**
	 * Get the results.
	 *
	 * @return Follower[]
	 */
	public function results(): array {
		return $this->results;
	}
}
