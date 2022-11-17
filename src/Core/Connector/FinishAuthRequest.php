<?php

namespace Smolblog\Core\Connector;

use Smolblog\Framework\Command;

/**
 * Command to kick off saving data from an OAuth callback.
 */
class FinishAuthRequest implements Command {
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
	}
}
