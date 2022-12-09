<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Framework\Command;

/**
 * Command to kick off saving data from an OAuth callback.
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
		public string $provider,
		public string $stateKey,
		public string $code,
	) {
	}
}
