<?php

namespace Smolblog\Core\Content\Types\Status;

use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Take a status from draft to Published
 */
class PublishStatus extends Command implements AuthorizableMessage {
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
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $statusId,
	) {
		$this->contentId = $this->statusId;
	}
}