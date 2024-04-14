<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Core\ContentV1\EditContentCommandKit;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Change the comment text on a Reblog.
 */
readonly class EditReblogComment extends Command implements AuthorizableMessage {
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
