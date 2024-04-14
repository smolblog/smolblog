<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Smolblog\Core\ContentV1\EditContentCommandKit;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Change the media on a Picture.
 */
class EditPictureMedia extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Media to display; will replace existing value.
	 *
	 * @var Identifier[]
	 */
	public readonly array $mediaIds;

	/**
	 * Construct the command.
	 *
	 * @param Identifier[] $mediaIds  Media to display; will replace existing value.
	 * @param Identifier   $contentId ID for the new picture; will auto-generate if not given.
	 * @param Identifier   $siteId    Site for this picture.
	 * @param Identifier   $userId    User authoring this picture.
	 */
	public function __construct(
		array $mediaIds,
		public readonly Identifier $contentId,
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
	) {
		$this->mediaIds = array_values($mediaIds);
	}
}
