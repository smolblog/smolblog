<?php

namespace Smolblog\Core\Content\Commands;

use DateTimeInterface;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Edit the base attributes of a piece of content.
 */
class ChangeContentVisibility extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Construct the command
	 *
	 * @param Identifier        $contentId  ID of content to edit.
	 * @param Identifier        $userId     ID of user making this change.
	 * @param Identifier        $siteId     ID of site this content exists on.
	 * @param ContentVisibility $visibility Updated visibility of the content.
	 */
	public function __construct(
		public readonly Identifier $contentId,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly ContentVisibility $visibility,
	) {
	}
}
