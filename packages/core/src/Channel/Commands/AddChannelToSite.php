<?php

namespace Smolblog\Core\Channel\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Give a Site permission to push to a Channel.
 */
readonly class AddChannelToSite implements Command, Authenticated {
	use ValueKit;

	/**
	 * Construct the command
	 *
	 * @param UuidInterface $channelId ID of the Channel to link.
	 * @param UuidInterface $siteId    ID of the Site to link.
	 * @param UuidInterface $userId    ID of the User initiating this command.
	 */
	public function __construct(
		public UuidInterface $channelId,
		public UuidInterface $siteId,
		public UuidInterface $userId,
	) {}
}
