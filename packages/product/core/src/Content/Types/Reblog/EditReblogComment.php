<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Change the comment text on a Reblog.
 */
class EditReblogComment extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId    Site this reblog is posted on.
	 * @param Identifier $userId    User making this change.
	 * @param Identifier $contentId Note being changed.
	 * @param string     $comment   New reblog comment.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $contentId,
		public readonly string $comment,
	) {
	}
}
