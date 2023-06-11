<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Framework\Messages\Command;

/**
 * Command to kick off saving data from an OAuth callback.
 */
class FinishAuthRequest extends Command {
	/**
	 * URL to return the user to.
	 *
	 * @var string|null
	 */
	public ?string $returnToUrl = null;

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
