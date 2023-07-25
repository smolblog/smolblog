<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Delete a note and remove it from projections.
 */
class DeleteNote extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId    Site this note is posted on.
	 * @param Identifier $userId    User making this change.
	 * @param Identifier $contentId Note being changed.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $contentId,
	) {
	}
}
