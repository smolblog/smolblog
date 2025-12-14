<?php

namespace Smolblog\Core\Connection\Commands;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Check the connection to see if any access tokens need to be refreshed.
 */
readonly class RefreshConnection extends Command {
	/**
	 * Create the command
	 *
	 * @param Identifier $connectionId Connection to refresh.
	 * @param Identifier $userId       User initiating the refresh.
	 */
	public function __construct(
		public Identifier $connectionId,
		public Identifier $userId,
	) {
		parent::__construct();
	}
}
