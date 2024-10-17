<?php

namespace Smolblog\Core\Media\Commands;

use Smolblog\Core\Media\Queries\UserCanEditMedia;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;

/**
 * Change the attributes on a media object.
 */
readonly class EditMediaAttributes extends Command {
	/**
	 * Construct the command.
	 *
	 * @throws InvalidValueProperties Thrown if no updated attributes are given.
	 *
	 * @param Identifier  $mediaId           ID of the media being edited.
	 * @param Identifier  $userId            User making this change.
	 * @param string|null $title             New title.
	 * @param string|null $accessibilityText New alt text.
	 */
	public function __construct(
		public readonly Identifier $mediaId,
		public readonly Identifier $userId,
		public readonly ?string $title = null,
		public readonly ?string $accessibilityText = null,
	) {
		if (!isset($title) && !isset($accessibilityText)) {
			throw new InvalidValueProperties('No updated attributes provided.');
		}
		if ((isset($title) && empty($title)) || (isset($accessibilityText) && empty($accessibilityText))) {
			throw new InvalidValueProperties('title and accessibilityText must not be empty.');
		}

		parent::__construct();
	}
}
