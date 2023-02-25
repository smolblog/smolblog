<?php

namespace Smolblog\Core\Connector\Services;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Connector\Commands\RefreshChannels;
use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\Services\ConnectorRegistry;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Events\ChannelDeleted;
use Smolblog\Core\Connector\Events\ChannelSaved;
use Smolblog\Core\Connector\Events\ConnectionEstablished;
use Smolblog\Core\Connector\Queries\ChannelsForConnection;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

final class ChannelRefresherTest extends TestCase {
	use EventComparisonTestKit;

	private Connection $connection;
	private ConnectorRegistry $connectors;
	private MessageBus $messageBus;
	private ChannelRefresher $service;
	private array $oldChannels;
	private array $newChannels;

	public function setUp(): void {
		$this->connection = new Connection(
			userId: Identifier::createRandom(),
			provider: 'something',
			providerKey: 'something',
			displayName: 'something',
			details: ['something'=>'else'],
		);

		$oldMain = new Channel(connectionId: $this->connection->id, channelKey: 'main.blog.co', displayName: 'Main Blog', details: []);
		$newMain = new Channel(connectionId: $this->connection->id, channelKey: 'main.blog.co', displayName: 'The Blog', details: []);
		$alt = new Channel(connectionId: $this->connection->id, channelKey: 'alt.blog.co', displayName: 'Alt Blog', details: []);
		$fake = new Channel(connectionId: $this->connection->id, channelKey: 'fake.blog.co', displayName: 'Fake Blog', details: []);

		$this->oldChannels = [$oldMain, $alt];
		$this->newChannels = [$newMain, $fake];

		$deleteEvent = new ChannelDeleted(
			channelKey: $alt->channelKey,
			connectionId: $this->connection->id,
			userId: $this->connection->userId
		);
		$saveEventFirst = new ChannelSaved(
			channelKey: $newMain->channelKey,
			displayName: $newMain->displayName,
			details: $newMain->details,
			connectionId: $this->connection->id,
			userId: $this->connection->userId
		);
		$saveEventSecond = new ChannelSaved(
			channelKey: $fake->channelKey,
			displayName: $fake->displayName,
			details: $fake->details,
			connectionId: $this->connection->id,
			userId: $this->connection->userId
		);

		$connector = $this->createMock(Connector::class);
		$connector->expects($this->once())->method('getChannels')->willReturn($this->newChannels);

		$this->connectors = $this->createStub(ConnectorRegistry::class);
		$this->connectors->method('get')->willReturn($connector);

		$this->messageBus = $this->createMock(MessageBus::class);
		$this->messageBus->expects($this->exactly(3))->method('dispatch')->withConsecutive(
			[$this->eventEquivalentTo($deleteEvent)],
			[$this->eventEquivalentTo($saveEventFirst)],
			[$this->eventEquivalentTo($saveEventSecond)],
		);

		$this->service = new ChannelRefresher(
			connectors: $this->connectors,
			messageBus: $this->messageBus,
		);
	}

	public function testItHandlesTheRefreshChannelsCommand(): void {
		$this->messageBus->expects($this->exactly(2))->method('fetch')->withConsecutive(
			[new ConnectionById(connectionId: $this->connection->id)],
			[new ChannelsForConnection(connectionId: $this->connection->id)],
		)->will($this->onConsecutiveCalls($this->connection, $this->oldChannels));

		$this->service->onRefreshChannels(
			new RefreshChannels(connectionId: $this->connection->id, userId: $this->connection->userId)
		);
	}

	public function testItHandlesTheConnectionEstablishedEvent(): void {
		$this->messageBus->expects($this->once())->method('fetch')->with(
			new ChannelsForConnection(connectionId: $this->connection->id)
		)->willReturn($this->oldChannels);

		$this->service->onConnectionEstablished(new ConnectionEstablished(
			provider: $this->connection->provider,
			providerKey: $this->connection->providerKey,
			displayName: $this->connection->displayName,
			details: $this->connection->details,
			connectionId: $this->connection->id,
			userId: $this->connection->userId
		));
	}
}
