<?php

namespace Smolblog\Core\Content\Types\Status;

use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Messages\StoppableMessageKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Delete a status and remove it from projections.
 */
class DeleteStatus extends Command implements AuthorizableMessage {
	use StoppableMessageKit;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId   Site this status is posted on.
	 * @param Identifier $userId   User making this change.
	 * @param Identifier $statusId Status being changed.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $statusId,
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
			contentId: $this->statusId,
		);
	}
}
