<?php

namespace Smolblog\Core\Federation;

use Smolblog\Foundation\Service\Messaging\MemoizableQuery;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Traits\Memoizable;
use Smolblog\Foundation\Value\Traits\MemoizableKit;

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
		parent::__construct();
	}
}
