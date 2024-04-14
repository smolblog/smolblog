<?php

namespace Smolblog\Core\Site;

use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get the users associated with a site.
 *
 * The querying user must be an author or admin on the site.
 */
readonly class SiteUsers extends Query implements AuthorizableMessage {
	/**
	 * Construct the query
	 *
	 * @param Identifier $siteId Site to query.
	 * @param Identifier $userId User making the query.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
	) {
	}

	/**
	 * Check if user is attached to the site.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserHasPermissionForSite(
			siteId: $this->siteId,
			userId: $this->userId,
			mustBeAuthor: true,
		);
	}
}
