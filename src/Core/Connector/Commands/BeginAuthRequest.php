<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * The indicated user is starting an OAuth request with the indicated provider.
 */
class BeginAuthRequest extends Command {
	/**
	 * Create the command
	 *
	 * @param string     $provider    Identifier for a registered Connector.
	 * @param Identifier $userId      Identifier for the authenticated User.
	 * @param string     $callbackUrl Callback URL to give to the provider.
	 */
	public function __construct(
		public readonly string $provider,
		public readonly Identifier $userId,
		public readonly string $callbackUrl,
	) {
	}
}
