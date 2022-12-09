<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Framework\Command;
use Smolblog\Framework\Identifier;

/**
 * Command to fetch and save an updated list of channels for a given Connection.
 */
readonly class RefreshChannels extends Command {
	/**
	 * Construct the command
	 *
	 * @param Identifier $connectionId ID of the Connection to get channels for.
	 */
	public function __construct(
		public Identifier $connectionId
	) {
	}
}
