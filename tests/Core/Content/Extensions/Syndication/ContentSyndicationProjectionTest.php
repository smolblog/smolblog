<?php

namespace Smolblog\Core\ContentV1\Extensions\Syndication;

use Illuminate\Database\Schema\Blueprint;
use Smolblog\Test\Kits\DatabaseTestKit;
use Smolblog\Test\TestCase;

final class ContentSyndicationProjectionTest extends TestCase{
	use DatabaseTestKit;

	private ContentSyndicationProjection $projection;

	protected function setUp(): void {
		$this->initDatabaseWithTable('content_syndication', function(Blueprint $table) {
			$table->uuid('row_uuid')->primary();
			$table->uuid('content_uuid');
			$table->uuid('channel_uuid')->nullable();
			$table->string('url')->nullable();
		});

		$this->projection = new ContentSyndicationProjection(db: $this->db);
	}

	private function insertRows(array $rows): void {
		$this->db->table('content_syndication')->insert(array_map(
			fn($k) => [
				'row_uuid' => ContentSyndicationProjection::rowIdFor($k[0], $k[1], $k[2]),
				'content_uuid' => $k[0]->toString(),
				'channel_uuid' => $k[1]?->toString() ?? null,
				'url' => $k[2] ?? null,
			],
			$rows,
		));
	}

	public function testItWillSetSyndicationChannels() {
		$event = new SyndicationChannelsSet(
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			channels: [
				$this->randomId(),
				$this->randomId(),
				$this->randomId(),
			],
		);

		$this->insertRows([
			[$this->randomId(), $this->randomId(), null],
			[$this->randomId(), null, '//eph.me/'],
			[$event->contentId, $this->randomId(), null],
			[$event->contentId, null, '//echoing.green/'],
			[$event->contentId, $this->randomId(), '//smol.blog/'],
			[$this->randomId(), $this->randomId(), null],
			[$event->contentId, $event->channels[0], null],
		]);

		$this->projection->onSyndicationChannelsSet($event);

		$this->assertEquals(8, $this->db->table('content_syndication')->count());
		$this->assertEquals(
			5,
			$this->db->table('content_syndication')->where('content_uuid', '=', $event->contentId->toString())->count()
		);
		$this->assertTrue(
			$this->db->table('content_syndication')->where([
				['content_uuid', '=', $event->contentId->toString()],
				['url', '=', '//echoing.green/'],
			])->exists()
		);
		$this->assertTrue(
			$this->db->table('content_syndication')->where([
				['content_uuid', '=', $event->contentId->toString()],
				['url', '=', '//smol.blog/'],
			])->exists()
		);
		foreach ($event->channels as $channelId) {
			$this->assertTrue(
				$this->db->table('content_syndication')->where([
					['content_uuid', '=', $event->contentId->toString()],
					['channel_uuid', '=', $channelId->toString()],
				])->exists()
			);
		}
		$this->assertFalse(
			$this->db->table('content_syndication')->
				where('content_uuid', '=', $event->contentId->toString())->
				whereNotIn('channel_uuid', array_map(fn($c) => $c->toString(), $event->channels))->
				whereNull('url')->
				exists()
		);
	}

	public function testItWillAddANewLinkWithNoChannel() {
		$event = new ContentSyndicated(
			url: '//smol.blog/',
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$this->projection->onContentSyndicated($event);
		$this->assertOnlyTableEntryEquals(
			table: $this->db->table('content_syndication'),
			row_uuid: ContentSyndicationProjection::rowIdFor(contentId: $event->contentId, url: '//smol.blog/'),
			content_uuid: $event->contentId->toString(),
			channel_uuid: null,
			url: '//smol.blog/',
		);
	}

	public function testItWillAddANewLinkWithAChannel() {
		$event = new ContentSyndicated(
			url: '//smol.blog/',
			channelId: $this->randomId(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$this->projection->onContentSyndicated($event);
		$this->assertOnlyTableEntryEquals(
			table: $this->db->table('content_syndication'),
			row_uuid: ContentSyndicationProjection::rowIdFor(contentId: $event->contentId, channelId: $event->channelId),
			content_uuid: $event->contentId->toString(),
			channel_uuid: $event->channelId->toString(),
			url: '//smol.blog/',
		);
	}

	public function testItWillUpdateALinkWithAChannel() {
		$event = new ContentSyndicated(
			url: '//smol.blog/',
			channelId: $this->randomId(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$this->db->table('content_syndication')->insert([
			'row_uuid' => ContentSyndicationProjection::rowIdFor(contentId: $event->contentId, channelId: $event->channelId),
			'content_uuid' => $event->contentId->toString(),
			'channel_uuid' => $event->channelId->toString(),
		]);

		$this->projection->onContentSyndicated($event);
		$this->assertOnlyTableEntryEquals(
			table: $this->db->table('content_syndication'),
			row_uuid: ContentSyndicationProjection::rowIdFor(contentId: $event->contentId, channelId: $event->channelId),
			content_uuid: $event->contentId->toString(),
			channel_uuid: $event->channelId->toString(),
			url: '//smol.blog/',
		);
	}

	public function testItWillSetSyndicationState() {
		$contentId = $this->randomId();
		$linkedChannelId = $this->randomId(true);
		$state = new Syndication(
			links: [
				new SyndicationLink(url: '//echoing.green/'),
				new SyndicationLink(url: '//smol.blog/', channelId: $linkedChannelId),
			],
			channels: [
				$this->randomId(true),
				$this->randomId(true),
			],
		);

		$this->insertRows([
			[$this->randomId(), $this->randomId(), null],
			[$this->randomId(), null, '//eph.me/'],
			[$contentId, $state->channels[0], null],
			[$contentId, null, '//echoing.green/'],
			[$contentId, $linkedChannelId, '//smol.blog/'],
			[$this->randomId(), $this->randomId(), '//oddevan.com/'],
			[$contentId, $state->channels[1], null],
		]);

		$message = $this->createMock(NeedsSyndicationState::class);
		$message->method('getContentId')->willReturn($contentId);
		$message->expects($this->once())->method('setSyndicationState')->with($this->equalTo($state));

		$this->projection->onNeedsSyndicationState($message);
	}
}
