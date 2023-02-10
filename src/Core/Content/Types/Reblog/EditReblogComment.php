<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Messages\StoppableMessageKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Change the comment text on a Reblog.
 */
class EditReblogComment extends Command implements AuthorizableMessage {
	use StoppableMessageKit;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId   Site this reblog is posted on.
	 * @param Identifier $userId   User making this change.
	 * @param Identifier $reblogId Status being changed.
	 * @param string     $comment  New reblog comment.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $reblogId,
		public readonly string $comment,
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
