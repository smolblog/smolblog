<?php

namespace Smolblog\Test;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Connector\Commands\BeginAuthRequest;
use Smolblog\Core\Connector\Commands\FinishAuthRequest;
use Smolblog\Core\Connector\Commands\RefreshChannels;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Queries\ChannelsForConnection;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Framework\Exceptions\MessageNotAuthorizedException;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Mock\App;

final class ConnectorTest extends TestCase {
	public function testAuthRequest() {
		$userId = Identifier::fromString('f7d1d707-bcf1-46bf-94d5-0c7942d51ca3');

		$command = new BeginAuthRequest(
			provider: 'smolblog',
			userId: $userId,
			callbackUrl: 'https://smol.blog/auth/smolblog/init',
		);
		App::dispatch($command);

		$this->assertEquals('https://smol.blog/oauth2/auth', $command->redirectUrl);

		App::dispatch(new FinishAuthRequest(
			provider: 'smolblog',
			stateKey: '1a8d8f64-7cb0-4083-9a8a-dcaf18dda186',
			code: '4cfe2924-948c-4109-ab18-b1755c831df0',
		));

		$result = App::fetch(new ConnectionById(
			Connection::buildId(provider: 'smolblog', providerKey: 'woohoo543')
		));

		$this->assertEquals(new Connection(
			userId: $userId,
			provider: 'smolblog',
			providerKey: 'woohoo543',
			displayName: 'snek.smol.blog',
			details: ['token' => '14me24you'],
		), $result);
	}

	/** @depends testAuthRequest */
	public function testRefreshChannelsSuccess() {
		$connectionId = Connection::buildId(provider: 'smolblog', providerKey: 'woohoo543');

		App::dispatch(new RefreshChannels(
			connectionId: $connectionId,
			userId: Identifier::fromString('f7d1d707-bcf1-46bf-94d5-0c7942d51ca3'),
		));

		$expected = new Channel(
			connectionId: $connectionId,
			channelKey: 'snek.smol.blog',
			displayName: 'snek.smol.blog',
			details: [],
		);
		$results = App::fetch(new ChannelsForConnection($connectionId));
		$this->assertEquals($expected, $results[0]);
	}

	/** @depends testAuthRequest */
	public function testRefreshChannelsFailure() {
		$this->expectException(MessageNotAuthorizedException::class);

		App::dispatch(new RefreshChannels(
			connectionId: Connection::buildId(provider: 'smolblog', providerKey: 'woohoo543'),
			userId: Identifier::fromString('d1701e97-3175-42d5-b6dd-f49df167e2a5'),
		));
	}
}
