<?php

namespace Smolblog\Core\Federation;

use Smolblog\Foundation\Service\Messaging\MemoizableQuery;

/**
 * Get the array of followers with the given provider+key.
 *
 * Used when something happens to the following entity, not just the single follower relationship.
 */
readonly class FollowersByProviderAndKey extends Query implements Memoizable {
	use MemoizableKit;
	/**
	 * Construct the query.
	 *
	 * @param string $provider    Provider the followers belong to.
	 * @param string $providerKey Provider key of the followers.
	 */
	public function __construct(
		public readonly string $provider,
		public readonly string $providerKey
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
