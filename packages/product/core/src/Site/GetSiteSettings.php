<?php

namespace Smolblog\Core\Site;

use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get the settings for the given site.
 */
readonly class GetSiteSettings extends Query implements AuthorizableMessage {
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
	 * Check if user is an admin on the site.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserHasPermissionForSite(
			siteId: $this->siteId,
			userId: $this->userId,
			mustBeAdmin:true,
		);
	}
}
