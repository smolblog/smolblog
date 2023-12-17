<?php

namespace Smolblog\Core\Syndication;

use DateTimeImmutable;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Queries\SiteHasPermissionForChannel;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentType;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Extensions\Syndication\Syndication;
use Smolblog\Core\Content\GenericContent;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\TestCase;

final class PushContentToChannelTest extends TestCase {
	private Connection $connection;
	private Channel $channel;
	private Content $content;

	protected function setUp(): void
	{
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
	}

	public function testItIsAuthorizedBySiteAndChannel() {
		$subject = new PushContentToChannel(
			content: $this->content,
			channel: $this->channel,
			connection: $this->connection
		);

		$expected = new SiteHasPermissionForChannel(
			siteId: $this->content->siteId,
			channelId: $this->channel->id,
			mustPush: true,
			mustPull: false,
		);

		$this->assertEquals($expected, $subject->getAuthorizationQuery());
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
