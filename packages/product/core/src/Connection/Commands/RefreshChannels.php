<?php

namespace Smolblog\Core\Connection\Commands;

use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Command to fetch and save an updated list of channels for a given Connection.
 */
readonly class RefreshChannels extends Command {
	/**
	 * Construct the command
	 *
	 * @param Identifier $connectionId ID of the Connection to get channels for.
	 * @param Identifier $userId       ID of the user initiating this Command.
	 */
	public function __construct(
		public readonly Identifier $connectionId,
		public readonly Identifier $userId,
	) {
		parent::__construct();
	}
}
