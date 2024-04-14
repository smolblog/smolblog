<?php

namespace Smolblog\Core\Site;

use Smolblog\Core\User\User;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Service\Messaging\MemoizableQuery;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Traits\Memoizable;
use Smolblog\Foundation\Value\Traits\MemoizableKit;

/**
 * Query to get the public and private keypair for a Site.
 */
readonly class GetSiteKeypair extends Query implements Memoizable, AuthorizableMessage {
	use MemoizableKit;
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
		return new readonly class ($this->userId) extends Query {
			/**
			 * Create the query.
			 *
			 * @param Identifier $userId ID of the user.
			 */
			public function __construct(public readonly Identifier $userId) {
				parent::__construct();
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
