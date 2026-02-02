<?php

namespace Smolblog\Core\Connection\Commands;

use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Command\ExpectedResponse;
use Cavatappi\Foundation\Value\ValueKit;
use Psr\Http\Message\UriInterface;

/**
 * Command to kick off saving data from an OAuth callback.
 *
 * Handler should return a redirect URL for returning the user to the application.
 */
#[ExpectedResponse(
	type: UriInterface::class,
	name: 'url',
	description: 'URL to redirect the end-user to.',
	optional: true,
)]
readonly class FinishAuthRequest implements Command {
	use ValueKit;

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
	) {}
}
