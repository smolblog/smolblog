<?php

namespace Smolblog\Core\Connection\Services;

use Smolblog\Core\Connection\Entities\AuthRequestState;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Entities\ConnectionInitData;
use Smolblog\Foundation\Service\Registry\Registerable;

/**
 * Class to handle authenticating against an external provider.
 */
interface ConnectionHandler extends Registerable {
	/**
	 * Get the string this ConnectionHandler should be registered under.
	 *
	 * Typically the provider name in all lowercase, e.g. 'tumblr', 'mastodon', or 'discord'.
	 *
	 * @return string
	 */
	public static function getKey(): string;

	/**
	 * Get the information needed to start an OAuth session with the provider
	 *
	 * @param string $callbackUrl URL of the callback endpoint.
	 * @return ConnectionHandlerInitData
	 */
	public function getInitializationData(string $callbackUrl): ConnectionInitData;

	/**
	 * Handle the OAuth callback from the provider and create the credential
	 *
	 * Implementing service should throw an exception if the Connection cannot be created.
	 *
	 * @param string           $code Code given to the OAuth callback.
	 * @param AuthRequestState $info Info from the original request.
	 * @return Connection Created credential
	 */
	public function createConnection(string $code, AuthRequestState $info): Connection;

	/**
	 * Get the channels enabled by the Connection.
	 *
	 * @param Connection $connection Account to get Channels for.
	 * @return Channel[] Array of Channels this Connection can use
	 */
	public function getChannels(Connection $connection): array;

	/**
	 * Check the connection to see if it needs to be refreshed.
	 *
	 * @param Connection $connection Connection object to check.
	 * @return boolean true if Connection requires a refresh.
	 */
	public function connectionNeedsRefresh(Connection $connection): bool;

	/**
	 * Refresh the given Connection and return the updated object.
	 *
	 * @param Connection $connection Connection object to refresh.
	 * @return Connection Refreshed Connection.
	 */
	public function refreshConnection(Connection $connection): Connection;
}
