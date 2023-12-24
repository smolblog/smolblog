<?php

namespace Smolblog\Core\Federation;

use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Framework\Objects\Identifier;

/**
 * Get the array of followers for the given site.
 */
class GetFollowersForSiteByProvider extends MemoizableQuery {
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
