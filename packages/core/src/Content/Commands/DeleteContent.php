<?php

namespace Smolblog\Core\Content\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Delete the given Content from the system.
 */
readonly class DeleteContent implements Command, Authenticated {
	use ValueKit;

	/**
	 * Create the Command.
	 *
	 * @param UuidInterface $userId    User performing the action.
	 * @param UuidInterface $contentId Content being deleted.
	 */
	public function __construct(
		public UuidInterface $userId,
		public UuidInterface $contentId,
	) {}
}
