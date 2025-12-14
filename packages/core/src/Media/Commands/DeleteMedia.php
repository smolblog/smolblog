<?php

namespace Smolblog\Core\Media\Commands;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Delete a media object.
 */
readonly class DeleteMedia extends Command {
	/**
	 * Construct the command.
	 *
	 * @param Identifier $userId  User making this change.
	 * @param Identifier $mediaId ID of the media being edited.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly Identifier $mediaId
	) {
		parent::__construct();
	}
}
