<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Take a picture from draft to Published
 */
class PublishPicture extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId    Site this picture is posted on.
	 * @param Identifier $userId    User making this change.
	 * @param Identifier $contentId Picture being changed.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $contentId,
	) {
	}
}
