<?php

namespace Smolblog\Core\Connector;

use Smolblog\Framework\Command;

/**
 * The indicated user is starting an OAuth request with the indicated provider.
 */
class BeginAuthRequest implements Command {
	/**
	 * Create the command
	 *
	 * @param string  $provider    Identifier for a registered Connector.
	 * @param integer $userId      Identifier for the authenticated User.
	 * @param string  $callbackUrl Callback URL to give to the provider.
	 */
	public function __construct(
		public readonly string $provider,
		public readonly int $userId,
		public readonly string $callbackUrl,
	) {
	}
}
