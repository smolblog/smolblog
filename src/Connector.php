<?php

namespace Smolblog\Core;

use Smolblog\Core\Definitions\HttpVerb;
use Smolblog\Core\Models\ExternalCredential;

/**
 * Class to handle authenticating against an external provider.
 */
interface Connector {
	/**
	 * Identifier for the provider.
	 *
	 * @return string
	 */
	public function slug(): string;

	/**
	 * Get the information needed to start an OAuth session with the provider
	 *
	 * @return array
	 */
	public function getInitializationData(): array;

	/**
	 * Handle the OAuth callback from the provider and create the credential
	 *
	 * @param string $code Code given to the OAuth callback.
	 * @param array $info Info from the original request.
	 * @return ExternalCredential Created credential
	 */
	public function createCredential(string $code, array $info = []): ExternalCredential;
}
