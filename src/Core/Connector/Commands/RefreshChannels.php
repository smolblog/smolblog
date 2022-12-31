<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Command to fetch and save an updated list of channels for a given Connection.
 */
class RefreshChannels extends Command {
	/**
	 * Construct the command
	 *
	 * @param Identifier $connectionId ID of the Connection to get channels for.
	 */
	public function __construct(
		public readonly Identifier $connectionId
	) {
	}
}
