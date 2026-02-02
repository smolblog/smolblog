<?php

namespace Smolblog\Core\Connection\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Command\ExpectedResponse;
use Cavatappi\Foundation\Value\ValueKit;
use Psr\Http\Message\UriInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * The indicated user is starting an OAuth request with the indicated handler.
 *
 * Handler should return the redirect URL given by the handler.
 */
#[ExpectedResponse(type: UriInterface::class, name: 'url', description: 'URL to redirect the end-user to.')]
readonly class BeginAuthRequest implements Command, Authenticated {
	use ValueKit;

	/**
	 * Create the command
	 *
	 * @param string        $handler     Identifier for a registered Connector.
	 * @param UuidInterface $userId      Identifier for the authenticated User.
	 * @param string        $callbackUrl Callback URL to give to the handler.
	 * @param string        $returnToUrl Optional URL to return the end-user to upon completion.
	 */
	public function __construct(
		public readonly string $handler,
		public readonly UuidInterface $userId,
		public readonly string $callbackUrl,
		public readonly ?string $returnToUrl = null,
	) {}
}
