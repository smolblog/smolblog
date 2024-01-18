<?php

namespace Smolblog\Core\Site;

use Smolblog\Core\User\User;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Query to get the public and private keypair for a Site.
 */
class GetSiteKeypair extends MemoizableQuery implements AuthorizableMessage {
	/**
	 * Create the query
	 *
	 * @param Identifier $siteId ID of the site.
	 * @param Identifier $userId ID of the user making the request.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
	) {
	}

	/**
	 * Get a query to determine if this user can view this site's private key.
	 *
	 * Currently returns an anonymous object that checks for the internal system user. This limits this query to
	 * internal use only.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new class ($this->userId) extends MemoizableQuery {
			/**
			 * Create the query.
			 *
			 * @param Identifier $userId ID of the user.
			 */
			public function __construct(public readonly Identifier $userId) {
				$this->stopMessage();
			}

			/**
			 * Make the check and return the results.
			 *
			 * @return boolean
			 */
			public function results(): bool {
				return strval($this->userId) === User::INTERNAL_SYSTEM_USER_ID;
			}
		};
	}
}
