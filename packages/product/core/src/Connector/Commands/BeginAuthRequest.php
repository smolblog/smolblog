<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * The indicated user is starting an OAuth request with the indicated provider.
 */
readonly class BeginAuthRequest extends Command {
	/**
	 * Create the command
	 *
	 * @param string     $provider    Identifier for a registered Connector.
	 * @param Identifier $userId      Identifier for the authenticated User.
	 * @param string     $callbackUrl Callback URL to give to the provider.
	 * @param string     $returnToUrl Optional URL to return the end-user to upon completion.
	 */
	public function __construct(
		public readonly string $provider,
		public readonly Identifier $userId,
		public readonly string $callbackUrl,
		public readonly ?string $returnToUrl = null,
	) {
		parent::__construct();
	}

	public function setRedirectUrl(string $url): void {
		$this->setMetaValue('redirectUrl', $url);
	}

	public function redirectUrl(): string {
		return $this->getMetaValue('redirectUrl');
	}
}
