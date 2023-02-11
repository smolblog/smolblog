<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Messages\StoppableMessageKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Set the tags for a piece of content.
 */
class SetTags extends Command implements AuthorizableMessage {
	use StoppableMessageKit;

	/**
	 * Create the command.
	 *
	 * @param Identifier $siteId    Site the content is on.
	 * @param Identifier $userId    User making the change.
	 * @param Identifier $contentId Content being modified.
	 * @param array      $tags      Text of the tags to set.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $contentId,
		public readonly array $tags,
	) {
	}

	/**
	 * The user must be able to edit the content.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserCanEditContent(
			userId: $this->userId,
			siteId: $this->siteId,
			contentId: $this->contentId,
		);
	}
}
