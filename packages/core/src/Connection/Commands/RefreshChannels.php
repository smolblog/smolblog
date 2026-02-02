<?php

namespace Smolblog\Core\Connection\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Command to fetch and save an updated list of channels for a given Connection.
 */
readonly class RefreshChannels implements Command, Authenticated {
	use ValueKit;

	/**
	 * Construct the command
	 *
	 * @param UuidInterface $connectionId ID of the Connection to get channels for.
	 * @param UuidInterface $userId       ID of the user initiating this Command.
	 */
	public function __construct(
		public readonly UuidInterface $connectionId,
		public readonly UuidInterface $userId,
	) {}
}
