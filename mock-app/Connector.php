<?php

namespace Smolblog\Mock;

use Smolblog\Core\Connector\Connector as ConnectorConnector;
use Smolblog\Core\Connector\ConnectorConfiguration;
use Smolblog\Core\Connector\ConnectorInitData;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\NoRefreshKit;
use Smolblog\Core\Content\Content;

class Connector implements ConnectorConnector {
	use NoRefreshKit;

	public static function getConfiguration(): ConnectorConfiguration {
		return new ConnectorConfiguration(
			key: 'smolblog',
		);
	}

	public function getInitializationData(string $callbackUrl): ConnectorInitData {
		return new ConnectorInitData(
			url: 'https://smol.blog/oauth2/auth',
			state: '1a8d8f64-7cb0-4083-9a8a-dcaf18dda186',
			info: ['pkce' => '3bcaf30d-7f8a-4926-ba78-2f3e673e4dfc'],
		);
	}

	public function createConnection(string $code, AuthRequestState $info): ?Connection {
		return new Connection(
			provider: 'smolblog',
			providerKey: 'woohoo543',
			userId: $info->userId,
			displayName: 'snek.smol.blog',
			details: ['token' => '14me24you'],
		);
	}

	public function getChannels(Connection $connection): array {
		return [
			new Channel(
				connectionId: $connection->id,
				channelKey: 'snek.smol.blog',
				displayName: 'snek.smol.blog',
				details: [],
			)
		];
	}

	public function push(Content $content, Channel $toChannel, Connection $withConnection): void {}
}
