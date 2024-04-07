<?php

namespace Smolblog\Core\Syndication;

use DateTimeImmutable;
use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Queries\ChannelById;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Core\Connector\Services\ConnectorRegistry;
use Smolblog\Core\ContentV1\Content;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Core\ContentV1\Events\PublicContentAdded;
use Smolblog\Core\ContentV1\Extensions\Syndication\Syndication;
use Smolblog\Core\ContentV1\GenericContent;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Test\TestCase;

final class SyndicationServiceTest extends TestCase {
	private MessageBus $bus;
	private Connector $connector;
	private Connection $connection;
	private Channel $channel;
	private Content $content;

	protected function setUp(): void {
		$this->connection = new Connection(
			userId: $this->randomId(),
			provider: 'smoltest',
			providerKey: '12345',
			displayName: 'Test5678',
			details: [],
		);
		$this->channel = new Channel(
			connectionId: $this->connection->id,
			channelKey: '67890',
			displayName: 'Test1234',
			details: [],
		);
		$this->content = new Content(
			type: new GenericContent('one', '<p>two</p>', 'test'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: $this->randomId()->toString(),
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			extensions: [
				Syndication::class => new Syndication(
					links: [],
					channels: [$this->channel->id],
				),
			],
		);

		$this->bus = $this->createMock(MessageBus::class);
		$this->bus->method('fetch')->willReturnCallback(fn($query) => match (get_class($query)) {
			ChannelById::class => $this->channel,
			ConnectionById::class => $this->connection,
		});

		$this->connector = $this->createMock(Connector::class);

		$reg = $this->createStub(ConnectorRegistry::class);
		$reg->method('get')->willReturn($this->connector);

		$this->subject = new SyndicationService(bus: $this->bus, connectors: $reg);
	}

	public function testItHandlesNewPublicContent() {
		$event = $this->createStub(PublicContentAdded::class);
		$event->method('getContent')->willReturn($this->content);

		$expected = new PushContentToChannel(
			content: $this->content,
			channel: $this->channel,
			connection: $this->connection,
		);

		$this->bus->expects($this->once())->method('dispatchAsync')->with($this->equalTo($expected));

		$this->subject->onPublicContentAdded($event);
	}


	public function testItPassesTheCommandToTheAppropriateConnector() {
		$command = new PushContentToChannel(
			content: $this->content,
			channel: $this->channel,
			connection: $this->connection,
		);

		$this->connector->expects($this->once())->method('push')->with(
			content: $this->equalTo($this->content),
			toChannel: $this->equalTo($this->channel),
			withConnection: $this->equalTo($this->connection),
		);

		$this->subject->onPushContentToChannel($command);
	}
}
