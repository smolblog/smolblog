<?php

namespace Smolblog\Core\Channel\Commands;

use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Asynchronous command to push the given content to the given channel.
 *
 * @internal Used by the DefaultChannelHandler superclass to start an asynchronous process.
 */
readonly class CompleteContentPush extends Command {
	/**
	 * Create the command.
	 *
	 * @param Content    $content      Content to push.
	 * @param Channel    $channel      Channel to push to.
	 * @param Identifier $userId       ID of the user who initiatied the push.
	 * @param Identifier $startEventId ID of the event starting the process.
	 * @param string     $service      DefaultChannelHandler subclass that should handle this command.
	 */
	public function __construct(
		public Content $content,
		public Channel $channel,
		public Identifier $userId,
		public Identifier $startEventId,
		public string $service,
	) {
		parent::__construct();
	}
}
