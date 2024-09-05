<?php

namespace Smolblog\Core\Connection\Commands;

use Smolblog\Foundation\Value\Messages\Command;

/**
 * Command to kick off saving data from an OAuth callback.
 *
 * Handler should return a redirect URL for returning the user to the application.
 */
readonly class FinishAuthRequest extends Command {
	/**
	 * Create the command
	 *
	 * @param string $provider ID for a registered Connector.
	 * @param string $stateKey ID for an AuthRequestState.
	 * @param string $code     Key for the OAuth request.
	 */
	public function __construct(
		public readonly string $provider,
		public readonly string $stateKey,
		public readonly string $code,
	) {
		parent::__construct();
	}
}
