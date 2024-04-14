<?php

namespace Smolblog\MicroBlog;

use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\ConnectorConfiguration;
use Smolblog\Core\Connector\ConnectorInitData;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\NoRefreshKit;
use Smolblog\Core\ContentV1\Content;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;

/**
 * Connection class for Micro.blog
 *
 * Here because Micro.blog has an open, free API unlike *some* services we could mention.
 */
class MicroBlogConnector implements Connector {
	use NoRefreshKit;

	/**
	 * Get the configuration for this connector.
	 *
	 * @return ConnectorConfiguration
	 */
	public static function getConfiguration(): ConnectorConfiguration {
		return new ConnectorConfiguration(
			key: 'microblog',
		);
	}

	/**
	 * Start an IndieAuth request with Micro.blog
	 *
	 * @param string $callbackUrl URL to redirect back to.
	 * @return ConnectorInitData
	 */
	public function getInitializationData(string $callbackUrl): ConnectorInitData {
		$state = (new RandomIdentifier())->toString();
		$args = [
			'redirect_uri' => $callbackUrl,
			'client_id' => 'https://smolblog.localhost/',
			'state' => $state,
			'scope' => 'create',
			'response_type' => 'code',
		];

		return new ConnectorInitData(
			url: 'https://micro.blog/indieauth/auth?' . http_build_query($args),
			state: $state,
			info: [],
		);
	}

	/**
	 * Finalize an IndieAuth request with Micro.blog
	 *
	 * @param string           $code Identification code from Micro.blog.
	 * @param AuthRequestState $info Stored information about the request.
	 * @return Connection|null
	 */
	public function createConnection(string $code, AuthRequestState $info): ?Connection {
		$response = file_get_contents('https://micro.blog/indieauth/token', false, stream_context_create([
			'http' => [
				'method' => 'POST',
				'header'  => "Content-type: application/x-www-form-urlencoded",
				'content' => http_build_query([
					'code' => $code,
					'client_id' => 'https://smolblog.localhost/',
					'grant_type' => 'authorization_code',
				]),
			],
		]));
		$authPayload = json_decode($response);

		return new Connection(
			provider: self::getSlug(),
			providerKey: $authPayload->me,
			userId: $info->userId,
			displayName: $authPayload->profile->name,
			details: ['token' => $authPayload->access_token],
		);
	}

	/**
	 * Get available blogs from Micro.blog
	 *
	 * @param Connection $connection Micro.blog connection to authenticate the request.
	 * @return array
	 */
	public function getChannels(Connection $connection): array {
		$authHeader = 'Bearer ' . $connection->details['token'];

		$response = file_get_contents('https://micro.blog/micropub?q=config', false, stream_context_create([
			'http' => [
				'method' => 'GET',
				'header'  => "Authorization: $authHeader",
			],
		]));
		$config = json_decode($response);

		return array_map(
			fn($dest) => new Channel(
				connectionId: $connection->id,
				channelKey: $dest->uid,
				displayName: $dest->name,
				details: [],
			),
			$config?->destination ?? []
		);
	}

	/**
	 * Push a new blog post to a blog. Currently blank.
	 *
	 * @param Content    $content        Content to syndicate.
	 * @param Channel    $toChannel      Blog to post to.
	 * @param Connection $withConnection Connection to use.
	 * @return void
	 */
	public function push(Content $content, Channel $toChannel, Connection $withConnection): void {
		// Need to send a properly-formatted Micropub request.
	}
}
