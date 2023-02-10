<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Messages\StoppableMessageKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Delete a reblog and remove it from projections.
 */
class DeleteReblog extends Command implements AuthorizableMessage {
	use StoppableMessageKit;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId   Site this reblog is posted on.
	 * @param Identifier $userId   User making this change.
	 * @param Identifier $reblogId Reblog being changed.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $reblogId,
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
