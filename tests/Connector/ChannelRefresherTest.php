<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;

final class ChannelRefresherTest extends TestCase {
	public function testItHandlesTheRefreshChannelsCommand(): void {
		$connection = new Connection(
			userId: 5,
			provider: 'something',
			providerKey: 'something',
			displayName: 'something',
			details: ['something'=>'else'],
		);

		$oldMain = new Channel(connectionId: $connection->id, channelKey: 'main.blog.co', displayName: 'Main Blog', details: []);
		$newMain = new Channel(connectionId: $connection->id, channelKey: 'main.blog.co', displayName: 'The Blog', details: []);
		$alt = new Channel(connectionId: $connection->id, channelKey: 'alt.blog.co', displayName: 'Alt Blog', details: []);
		$fake = new Channel(connectionId: $connection->id, channelKey: 'fake.blog.co', displayName: 'Fake Blog', details: []);

		$oldChannels = [$oldMain, $alt];
		$newChannels = [$newMain, $fake];

		$connector = $this->createMock(Connector::class);
		$connector->expects($this->once())->method('getChannels')->willReturn($newChannels);

		$connectors = $this->createStub(ConnectorRegistrar::class);
		$connectors->method('get')->willReturn($connector);

		$connections = $this->createStub(ConnectionReader::class);
		$connections->method('get')->willReturn($connection);

		$channelReader = $this->createMock(ChannelReader::class);
		$channelReader->expects($this->once())->method('getChannelsFor')->willReturn($oldChannels);

		$channelWriter = $this->createMock(ChannelWriter::class);
		$channelWriter->expects($this->once())->method('delete')->with($this->equalTo($alt->id));
		$channelWriter->expects($this->exactly(2))->method('save')->withConsecutive([$this->equalTo($newMain)], [$this->equalTo($fake)]);

		$service = new ChannelRefresher(
			connections: $connections,
			connectors: $connectors,
			channelReader: $channelReader,
			channelWriter: $channelWriter,
		);

		$service->handleRefreshChannels(new RefreshChannels(connectionId: $connection->id));
	}
}
