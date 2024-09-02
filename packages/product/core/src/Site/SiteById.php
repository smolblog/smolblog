<?php

namespace Smolblog\Core\Site;

use Smolblog\Framework\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Retrieve a Site by ID
 */
class SiteById extends Query {
	/**
	 * Construct the query
	 *
	 * @param Identifier $siteId ID of the site.
	 */
	public function __construct(
		public readonly Identifier $siteId
	) {
	}
}
