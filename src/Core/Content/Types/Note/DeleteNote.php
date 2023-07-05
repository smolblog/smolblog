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
	 * Map noteId to contentID so we can use the trait.
	 *
	 * @var Identifier
	 */
	private Identifier $contentId;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId Site this note is posted on.
	 * @param Identifier $userId User making this change.
	 * @param Identifier $noteId Note being changed.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $noteId,
	) {
		$this->contentId = $this->noteId;
	}
}
