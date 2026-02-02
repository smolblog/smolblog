<?php

namespace Smolblog\Core\Media\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Delete a media object.
 */
readonly class DeleteMedia implements Command, Authenticated {
	use ValueKit;

	/**
	 * Construct the command.
	 *
	 * @param UuidInterface $userId  User making this change.
	 * @param UuidInterface $mediaId ID of the media being edited.
	 */
	public function __construct(
		public readonly UuidInterface $userId,
		public readonly UuidInterface $mediaId,
	) {}
}
