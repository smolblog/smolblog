<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Messages\StoppableMessageKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Change the URL on a Reblog.
 *
 * Could also be the same URL if the data needs to be refreshed.
 */
class EditReblogUrl extends Command implements AuthorizableMessage {
	use StoppableMessageKit;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId   Site this reblog is posted on.
	 * @param Identifier $userId   User making this change.
	 * @param Identifier $reblogId Reblog being changed.
	 * @param string     $url      New URL being reblogged.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $reblogId,
		public readonly string $url,
	) {
	}

	/**
	 * User must be able to edit this content.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserCanEditContent(
			userId: $this->userId,
			siteId: $this->siteId,
			contentId: $this->reblogId,
		);
	}
}
