<?php

namespace Smolblog\Api\Connector;

use Smolblog\Test\TestCase;
use Smolblog\Api\GenericResponse;
use Smolblog\Test\Kits\EndpointTestToolkit;
use Smolblog\Core\Connector\Entities\Channel as ChannelEntity;
use Smolblog\Core\Connector\Entities\Connection as ConnectionEntity;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

final class UserConnectionsTest extends TestCase {
	use EndpointTestToolkit;
	const ENDPOINT = UserConnections::class;

	public function testItReturnsConnectionsAndChannels() {
		$connection = new ConnectionEntity(
			userId: $this->randomId(),
			provider: 'smolblog',
			providerKey: 'windfox',
			displayName: '@windfox@smol.blog',
			details: [],
		);
		$channels = [
			new ChannelEntity(connectionId: $connection->id, channelKey: 'snek', displayName: 'snek.smol.blog', details: []),
			new ChannelEntity(connectionId: $connection->id, channelKey: 'dotorg', displayName: 'smolblog.org', details: []),
			new ChannelEntity(connectionId: $connection->id, channelKey: 'fox', displayName: 'fox.smol.blog', details: []),
		];

		$bus = $this->createStub(MessageBus::class);
		$bus->method('fetch')->willReturn([$connection], $channels);

		$endpoint = new UserConnections($bus);
		$result = $endpoint->run(userId: $connection->userId);

		$this->assertEquals(
			new GenericResponse(connections: [new Connection(
				id: $connection->id,
				userId: $connection->userId,
				provider: $connection->provider,
				providerKey: $connection->providerKey,
				displayName: $connection->displayName,
				channels: array_map(
					fn($c) => new Channel(id: $c->id, channelKey: $c->channelKey, displayName: $c->displayName),
					$channels,
				),
			)]),
			$result
		);
	}
}
