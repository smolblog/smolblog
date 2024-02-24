<?php

namespace Smolblog\ActivityPub\Handles;

use Smolblog\Framework\Messages\Query;

/**
 * Get the site for a given ActivityPub handle.
 */
class GetSiteByHandle extends Query {
	/**
	 * Construct the query.
	 *
	 * @param string $handle ActivityPub handle.
	 */
	public function __construct(
		public readonly string $handle,
	) {
	}
}
