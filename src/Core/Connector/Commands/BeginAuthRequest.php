<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Framework\Command;

/**
 * The indicated user is starting an OAuth request with the indicated provider.
 */
readonly class BeginAuthRequest extends Command {
	/**
	 * Create the command
	 *
	 * @param string  $provider    Identifier for a registered Connector.
	 * @param integer $userId      Identifier for the authenticated User.
	 * @param string  $callbackUrl Callback URL to give to the provider.
	 */
	public function __construct(
		public string $provider,
		public int $userId,
		public string $callbackUrl,
	) {
	}
}
