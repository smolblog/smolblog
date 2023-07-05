<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Change the text on a Note.
 */
class EditNote extends Command implements AuthorizableMessage {
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
	 * @param string     $text   New note text.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $noteId,
		public readonly string $text,
	) {
		$this->contentId = $this->noteId;
	}
}
