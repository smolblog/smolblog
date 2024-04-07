<?php

namespace Smolblog\Core\Syndication;

use DateTimeImmutable;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Queries\SiteHasPermissionForChannel;
use Smolblog\Core\ContentV1\Content;
use Smolblog\Core\ContentV1\ContentType;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Core\ContentV1\Extensions\Syndication\Syndication;
use Smolblog\Core\ContentV1\GenericContent;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\Kits\SerializableTestKit;
use Smolblog\Test\TestCase;

final class PushContentToChannelTest extends TestCase {
	use SerializableTestKit;

	private Connection $connection;
	private Channel $channel;
	private Content $content;

	protected function setUp(): void
	{
		$this->connection = new Connection(
			userId: $this->randomId(true),
			provider: 'smoltest',
			providerKey: '12345',
			displayName: 'Test5678',
			details: [],
		);
		$this->channel = new Channel(
			connectionId: Identifier::fromString($this->connection->id->toString()),
			channelKey: '67890',
			displayName: 'Test1234',
			details: [],
		);
		$this->content = new Content(
			id: $this->randomId(true),
			type: new GenericContent('one', '<p>two</p>', 'test'),
			siteId: $this->randomId(true),
			authorId: $this->randomId(true),
			permalink: $this->randomId(true)->toString(),
			publishTimestamp: new DateTimeImmutable('2023-12-23T20:53:49.090000+0000'),
			visibility: ContentVisibility::Published,
			extensions: [
				Syndication::class => new Syndication(
					links: [],
					channels: [Identifier::fromString($this->channel->id->toString())],
				),
			],
		);

		$this->subject = new PushContentToChannel(
			content: $this->content,
			channel: $this->channel,
			connection: $this->connection
		);
	}

	public function testItIsAuthorizedBySiteAndChannel() {
		$expected = new SiteHasPermissionForChannel(
			siteId: $this->content->siteId,
			channelId: $this->channel->id,
			mustPush: true,
			mustPull: false,
		);

		$this->assertEquals($expected, $this->subject->getAuthorizationQuery());
	}

	public function testItChecksForPublicContent() {
		$this->expectException(InvalidCommandParametersException::class);

		$badContent = Content::fromArray([
			...$this->content->toArray(),
			'visibility' => 'draft',
		]);

		new PushContentToChannel(
			content: $badContent,
			channel: $this->channel,
			connection: $this->connection,
		);
	}

	public function testItChecksForMatchingChannelAndConnection() {
		$this->expectException(InvalidCommandParametersException::class);

		$badChannel = Channel::fromArray([
			...$this->channel->toArray(),
			'connectionId' => $this->randomId()->toString(),
		]);

		new PushContentToChannel(
			content: $this->content,
			channel: $badChannel,
			connection: $this->connection,
		);
	}

	public function testItChecksForMatchingChannelAndContent() {
		$this->expectException(InvalidCommandParametersException::class);

		$badContent = Content::fromArray([
			...$this->content->toArray(),
			'extensions' => [],
		]);

		new PushContentToChannel(
			content: $badContent,
			channel: $this->channel,
			connection: $this->connection,
		);
	}
}
