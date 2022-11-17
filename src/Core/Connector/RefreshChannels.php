<?php

namespace Smolblog\Core\Connector;

use Smolblog\Framework\Command;

/**
 * Command to fetch and save an updated list of channels for a given Connection.
 */
class RefreshChannels implements Command {
	/**
	 * Construct the command
	 *
	 * @param string $connectionId ID of the Connection to get channels for.
	 */
	public function __construct(
		public readonly string $connectionId
	) {
	}
}
