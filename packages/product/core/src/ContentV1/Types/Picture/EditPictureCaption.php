<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Smolblog\Core\ContentV1\EditContentCommandKit;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Change the caption on a Picture.
 */
class EditPictureCaption extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Construct the command.
	 *
	 * @param string     $caption   Markdown caption; will replace existing value.
	 * @param Identifier $contentId ID for the new picture; will auto-generate if not given.
	 * @param Identifier $siteId    Site for this picture.
	 * @param Identifier $userId    User authoring this picture.
	 */
	public function __construct(
		public readonly string $caption,
		public readonly Identifier $contentId,
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
	) {
	}
}
