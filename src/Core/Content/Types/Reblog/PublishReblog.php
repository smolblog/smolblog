<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Take a status from draft to Published
 */
class PublishReblog extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Map reblogId to contentID so we can use the trait.
	 *
	 * @var Identifier
	 */
	private Identifier $contentId;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId   Site this status is posted on.
	 * @param Identifier $userId   User making this change.
	 * @param Identifier $reblogId Reblog being changed.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $reblogId,
	) {
		$this->contentId = $this->reblogId;
	}
}
