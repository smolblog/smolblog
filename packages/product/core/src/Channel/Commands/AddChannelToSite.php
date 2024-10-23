<?php

namespace Smolblog\Core\Channel\Commands;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Give a Site permission to push to a Channel.
 */
readonly class AddChannelToSite extends Command {
	/**
	 * Construct the command
	 *
	 * @param Identifier $channelId ID of the Channel to link.
	 * @param Identifier $siteId    ID of the Site to link.
	 * @param Identifier $userId    ID of the User initiating this command.
	 */
	public function __construct(
		public Identifier $channelId,
		public Identifier $siteId,
		public Identifier $userId,
	) {
		parent::__construct();
	}
}
