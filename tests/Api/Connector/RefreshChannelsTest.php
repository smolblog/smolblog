<?php

namespace Smolblog\Api\Connector;

use Smolblog\Test\TestCase;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Core\Connector\Commands\RefreshChannels as RefreshCommand;
use Smolblog\Core\Connector\Entities\Channel as ChannelEntity;
use Smolblog\Core\Connector\Entities\Connection as ConnectionEntity;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\Kits\EndpointTestToolkit;

final class RefreshChannelsTest extends TestCase {
	use EndpointTestToolkit;
	const ENDPOINT = RefreshChannels::class;

	public function testItGivesNotFoundIfTheConnectionIsInvalid() {
		$this->expectException(NotFound::class);

		$endpoint = new RefreshChannels($this->createStub(MessageBus::class));
		$endpoint->run(
			userId: $this->randomId(),
			params: ['id' => $this->randomId()],
		);
	}

	public function testItRefreshesChannelsWithAllParameters() {
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

		$command = new RefreshCommand(
			connectionId: $connection->id,
			userId: $connection->userId,
		);

		$bus = $this->mockBusExpects($command);
		$bus->method('fetch')->willReturn($connection, $channels);

		$endpoint = new RefreshChannels($bus);
		$result = $endpoint->run(userId: $command->userId, params: ['id' => $command->connectionId]);

		$this->assertEquals(
			new Connection(
				id: $connection->id,
				userId: $connection->userId,
				provider: $connection->provider,
				providerKey: $connection->providerKey,
				displayName: $connection->displayName,
				channels: array_map(
					fn($c) => new Channel(id: $c->id, channelKey: $c->channelKey, displayName: $c->displayName),
					$channels,
				),
			),
			$result
		);
	}
}
