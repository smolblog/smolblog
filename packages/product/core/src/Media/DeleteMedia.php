<?php

namespace Smolblog\Core\ContentV1\Media;

use Smolblog\Core\ContentV1\EditContentCommandKit;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Delete a media object.
 */
class DeleteMedia extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $contentId ID of the media being edited.
	 * @param Identifier $siteId    ID of the site holding the media.
	 * @param Identifier $userId    User making this change.
	 */
	public function __construct(
		public readonly Identifier $contentId,
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
	) {
	}
}
