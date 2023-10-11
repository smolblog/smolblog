<?php

namespace Smolblog\Tumblr;

use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\ConnectorInitData;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\NoRefreshKit;

/**
 * Connect to Tumblr.
 */
class TumblrConnector implements Connector {
	use NoRefreshKit;

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

	/**
	 * Create the service.
	 *
	 * @param TumblrClientFactory $factory Generate Tumblr clients.
	 */
	public function __construct(private TumblrClientFactory $factory) {
	}

	/**
	 * Get the information needed to start an OAuth session with the provider
	 *
	 * @param string $callbackUrl URL of the callback endpoint.
	 * @return ConnectorInitData
	 */
	public function getInitializationData(string $callbackUrl): ConnectorInitData {
		$handler = $this->factory->getAppClient()->getRequestHandler();
		$handler->setBaseUrl('https://www.tumblr.com/');

		$resp = $handler->request('POST', 'oauth/request_token', ['oauth_callback' => $callbackUrl]);
		$data = [];
		parse_str($resp->body, $data);

		return new ConnectorInitData(
			url: 'https://www.tumblr.com/oauth/authorize?oauth_token=' . $data['oauth_token'],
			state: $data['oauth_token'],
			info: ['secret' => $data['oauth_token_secret']],
		);
	}

	/**
	 * Handle the OAuth callback from the provider and create the credential
	 *
	 * @param string           $code Code given to the OAuth callback.
	 * @param AuthRequestState $info Info from the original request.
	 * @return null|Connection Created credential, null on failure
	 */
	public function createConnection(string $code, AuthRequestState $info): ?Connection {
		$handler = $this->factory->getUserClient(key: $info->key, secret: $info->info['secret'])->getRequestHandler();
		$handler->setBaseUrl('https://www.tumblr.com/');

		$accessResponse = $handler->request('POST', 'oauth/access_token', ['oauth_verifier' => $code]);

		$accessInfo = [];
		parse_str($accessResponse->body, $accessInfo);

		$client = $this->factory->getUserClient(
			key: $accessInfo['oauth_token'],
			secret: $accessInfo['oauth_token_secret']
		);
		$user = $client->getUserInfo()->user;

		return new Connection(
			userId: $info->userId,
			provider: self::getSlug(),
			providerKey: $this->findPrimaryBlogId($user->blogs),
			displayName: $user->name,
			details: [
				'key' => $accessInfo['oauth_token'],
				'secret' => $accessInfo['oauth_token_secret']
			],
		);
	}

	/**
	 * Get the channels enabled by the Connection.
	 *
	 * @param Connection $connection Account to get Channels for.
	 * @return Channel[] Array of Channels this Connection can use
	 */
	public function getChannels(Connection $connection): array {
		$client = $this->factory->getUserClient(...$connection->details);
		$user = $client->getUserInfo()->user;

		return array_map(
			fn($blog) => new Channel(
				connectionId: $connection->id,
				channelKey: $blog->uuid,
				displayName: "$blog->name",
				details: []
			),
			$user->blogs
		);
	}

	/**
	 * Get the primary blog from the user's list of blogs.
	 *
	 * @param array $blogs User's blogs.
	 * @return string Primary blog UUID
	 */
	private function findPrimaryBlogId(array $blogs): string {
		foreach ($blogs as $blog) {
			if ($blog->primary) {
				return $blog->uuid;
			}
		}
		return $blogs[0]->uuid;
	}
}
