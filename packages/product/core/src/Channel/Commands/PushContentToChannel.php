<?php

namespace Smolblog\Core\Channel\Commands;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Push the given content to the given channel.
 */
readonly class PushContentToChannel extends Command {
	/**
	 * Create the command.
	 *
	 * @param Identifier $contentId Content to push.
	 * @param Identifier $userId    User making the push.
	 * @param Identifier $channelId Channel to push content to.
	 */
	public function __construct(
		public Identifier $contentId,
		public Identifier $userId,
		public Identifier $channelId,
	) {
		parent::__construct();
	}
}
