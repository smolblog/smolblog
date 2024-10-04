<?php

namespace Smolblog\Core\Content\Commands;

use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Delete the given Content from the system.
 */
readonly class DeleteContent extends Command {
	/**
	 * Create the Command.
	 *
	 * @param Identifier $userId    User performing the action.
	 * @param Identifier $contentId Content being deleted.
	 */
	public function __construct(
		public Identifier $userId,
		public Identifier $contentId,
	) {
		parent::__construct();
	}
}
