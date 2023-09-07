<?php

namespace Smolblog\Tumblr;

use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\ConnectorInitData;
use Tumblr\API\Client as TumblrClient;

class TumblrConnector implements Connector {
	/**
	 * Get the string this Connector should be registered under.
	 *
	 * Typically the provider name in all lowercase, e.g. 'tumblr', 'mastodon', or 'discord'.
	 *
	 * @return string
	 */
	public static function getSlug(): string {
		return 'tumblr';
	}

	public function __construct(private TumblrClient $tumblr) {
	}

	/**
	 * Get the information needed to start an OAuth session with the provider
	 *
	 * @param string $callbackUrl URL of the callback endpoint.
	 * @return ConnectorInitData
	 */
	public function getInitializationData(string $callbackUrl): ConnectorInitData {
		$handler = $this->tumblr->getRequestHandler();
		$handler->setBaseUrl('https://www.tumblr.com/');

		$resp = $handler->request('POST', 'oauth/request_token', ['oauth_callback' => $callbackUrl]);
		$data = [];
		parse_str($resp->body, $data);

		return new ConnectorInitData(
			url: 'https://www.tumblr.com/oauth/authorize?oauth_token=' . $data['oauth_token'],
			state:
		)
	}

	/**
	 * Handle the OAuth callback from the provider and create the credential
	 *
	 * @param string           $code Code given to the OAuth callback.
	 * @param AuthRequestState $info Info from the original request.
	 * @return null|Connection Created credential, null on failure
	 */
	public function createConnection(string $code, AuthRequestState $info): ?Connection;

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
