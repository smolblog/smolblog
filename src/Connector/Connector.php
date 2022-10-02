<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Registrar\Registerable;

/**
 * Class to handle authenticating against an external provider.
 */
interface Connector extends Registerable {
	/**
	 * Configuration for the provider.
	 *
	 * @return ConnectorConfig
	 */
	public static function config(): ConnectorConfig;

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
	 * @param string           $code Code given to the OAuth callback.
	 * @param AuthRequestState $info Info from the original request.
	 * @return null|Connection Created credential, null on failure
	 */
	public function createConnection(string $code, AuthRequestState $info): ?Connection;
}
