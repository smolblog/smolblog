<?php

namespace Smolblog\Core\ContentV1\Media;

use Smolblog\Core\ContentV1\EditContentCommandKit;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Change the attributes on a media object.
 *
 * @deprecated Migrate to Smolblog\Core\Media
 */
class EditMediaAttributes extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Construct the command.
	 *
	 * @throws InvalidCommandParametersException Thrown if no updated attributes are given.
	 *
	 * @param Identifier  $contentId         ID of the media being edited.
	 * @param Identifier  $siteId            ID of the site holding the media.
	 * @param Identifier  $userId            User making this change.
	 * @param string|null $title             New title.
	 * @param string|null $accessibilityText New alt text.
	 */
	public function __construct(
		public readonly Identifier $contentId,
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly ?string $title = null,
		public readonly ?string $accessibilityText = null,
	) {
		if (!isset($title) && !isset($accessibilityText)) {
			throw new InvalidCommandParametersException(command: $this);
		}
	}
}
