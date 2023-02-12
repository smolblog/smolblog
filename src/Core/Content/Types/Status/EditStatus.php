<?php

namespace Smolblog\Core\Content\Types\Status;

use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Messages\StoppableMessageKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Change the text on a Status.
 */
class EditStatus extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Map statusId to contentID so we can use the trait.
	 *
	 * @var Identifier
	 */
	private Identifier $contentId;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId   Site this status is posted on.
	 * @param Identifier $userId   User making this change.
	 * @param Identifier $statusId Status being changed.
	 * @param string     $text     New status text.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $statusId,
		public readonly string $text,
	) {
		$this->contentId = $this->statusId;
	}
}
