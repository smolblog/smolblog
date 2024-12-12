<?php

namespace Smolblog\Core\Connection\Commands;

use Smolblog\Foundation\Service\Command\ExpectedResponse;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * The indicated user is starting an OAuth request with the indicated handler.
 *
 * Handler should return the redirect URL given by the handler.
 */
#[ExpectedResponse(type: Url::class, name: 'url', description: 'URL to redirect the end-user to.')]
readonly class BeginAuthRequest extends Command {
	/**
	 * Create the command
	 *
	 * @param string     $handler     Identifier for a registered Connector.
	 * @param Identifier $userId      Identifier for the authenticated User.
	 * @param string     $callbackUrl Callback URL to give to the handler.
	 * @param string     $returnToUrl Optional URL to return the end-user to upon completion.
	 */
	public function __construct(
		public readonly string $handler,
		public readonly Identifier $userId,
		public readonly string $callbackUrl,
		public readonly ?string $returnToUrl = null,
	) {
		parent::__construct();
	}
}
