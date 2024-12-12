<?php

namespace Smolblog\Core\Connection\Commands;

use Smolblog\Foundation\Service\Command\ExpectedResponse;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Command to kick off saving data from an OAuth callback.
 *
 * Handler should return a redirect URL for returning the user to the application.
 */
#[ExpectedResponse(type: Url::class, name: 'url', description: 'URL to redirect the end-user to.', optional: true)]
readonly class FinishAuthRequest extends Command {
	/**
	 * Create the command
	 *
	 * @param string $handler  ID for a registered Connector.
	 * @param string $stateKey ID for an AuthRequestState.
	 * @param string $code     Key for the OAuth request.
	 */
	public function __construct(
		public readonly string $handler,
		public readonly string $stateKey,
		public readonly string $code,
	) {
		parent::__construct();
	}
}
