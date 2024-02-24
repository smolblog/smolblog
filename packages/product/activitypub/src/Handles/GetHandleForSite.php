<?php

namespace Smolblog\ActivityPub\Handles;

use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Get the ActivityPub handle for a site.
 */
class GetHandleForSite extends Query {
	/**
	 * Construct the query.
	 *
	 * @param Identifier $siteId ID of the site.
	 */
	public function __construct(
		public readonly Identifier $siteId,
	) {
	}
}
