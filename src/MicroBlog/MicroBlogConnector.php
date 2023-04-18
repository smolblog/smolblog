<?php

namespace Smolblog\MicroBlog;

use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\ConnectorInitData;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\NoRefreshKit;
use Smolblog\Framework\Objects\Identifier;

class MicroBlogConnector implements Connector {
	use NoRefreshKit;

	public static function getSlug(): string
	{
		return 'microblog';
	}

	public function getInitializationData(string $callbackUrl): ConnectorInitData {
		$state = Identifier::createRandom()->toString();
		$args = [
			'redirect_uri' => $callbackUrl,
			'client_id' => 'https://smolblog.localhost/',
			'state' => $state,
			'scope' => 'create',
			'response_type' => 'code',
		];

		return new ConnectorInitData(
			url: 'https://micro.blog/indieauth/auth' . http_build_query($args),
			state: $state,
			info: [],
		);
	}

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
}
