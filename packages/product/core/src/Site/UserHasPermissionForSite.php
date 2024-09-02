<?php

namespace Smolblog\Core\Site;

use Smolblog\Framework\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Check if a given user can perform a given action for a given site.
 */
class UserHasPermissionForSite extends Query {
	/**
	 * Construct the query.
	 *
	 * @param Identifier $siteId       ID of the site in question.
	 * @param Identifier $userId       ID of the user in question.
	 * @param boolean    $mustBeAuthor True if the user must have Author-level permissions.
	 * @param boolean    $mustBeAdmin  True if the user must have Admin-level permissions.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly bool $mustBeAuthor = false,
		public readonly bool $mustBeAdmin = false,
	) {
	}
}
