<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Change the URL on a Reblog.
 *
 * Could also be the same URL if the data needs to be refreshed.
 */
class EditReblogUrl extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Mirror the reblogId into contentId so we can use the trait.
	 *
	 * @var Identifier
	 */
	private Identifier $contentId;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId   Site this reblog is posted on.
	 * @param Identifier $userId   User making this change.
	 * @param Identifier $reblogId Reblog being changed.
	 * @param string     $url      New URL being reblogged.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $reblogId,
		public readonly string $url,
	) {
		$this->contentId = $this->reblogId;
	}
}
