<?php

namespace Smolblog\Core\Connector;

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
	 * @param string $callbackUrl URL of the callback endpoint.
	 * @return ConnectorInitData
	 */
	public function getInitializationData(string $callbackUrl): ConnectorInitData;

	/**
	 * Handle the OAuth callback from the provider and create the credential
	 *
	 * @param string $code Code given to the OAuth callback.
	 * @param array  $info Info from the original request.
	 * @return null|ConnectionCredential Created credential, null on failure
	 */
	public function createCredential(string $code, array $info = []): ?ConnectionCredential;
}
