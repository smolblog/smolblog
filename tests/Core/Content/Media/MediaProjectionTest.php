<?php

namespace Smolblog\Core\Content\Media;

use DateTimeInterface;
use Illuminate\Database\Schema\Blueprint;
use Smolblog\Core\Content\Queries\ContentVisibleToUser;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\DatabaseTestKit;
use Smolblog\Test\TestCase;

final class MediaProjectionTest extends TestCase {
	use DatabaseTestKit;

	private MediaProjection $projection;

	protected function setUp(): void {
		$this->initDatabaseWithTable('media', function(Blueprint $table) {
			$table->uuid('content_uuid')->primary();
			$table->uuid('user_uuid');
			$table->uuid('site_uuid');
			$table->string('title');
			$table->string('accessibility_text');
			$table->string('type');
			$table->string('thumbnail_url');
			$table->string('default_url');
			$table->jsonb('file');
			$table->string('uploaded_at');
		});

		$this->projection = new MediaProjection(db: $this->db);
	}

	public function testItWillAddMedia() {
		$event = new MediaAdded(
			contentId: Identifier::fromString('696a2139-e951-4c69-8e58-b0830b3fee01'),
			userId: Identifier::fromString('959e3f76-0e5b-4efb-9a6f-4702d864e1ad'),
			siteId: Identifier::fromString('cbf9914d-95d8-4b1f-84b7-be20a85e4edf'),
			title: 'Uploaded Image',
			accessibilityText: 'Troy enters the apartment with pizza and sees the fire.',
			type: MediaType::Image,
			thumbnailUrl: '//cdn.smol.blog/thumb.gif',
			defaultUrl: '//cdn.smol.blog/scene.gif',
			file: new MediaFile(
				id: Identifier::fromString('bbc80cb4-bf9b-40b5-ad4b-afca7bc79f18'),
				handler: 'netflare',
				details: ['one' => 'two'],
				mimeType: 'image/gif',
			),
		);

		$this->projection->onMediaAdded($event);
		$this->assertOnlyTableEntryEquals(
			table: $this->db->table('media'),
			content_uuid: '696a2139-e951-4c69-8e58-b0830b3fee01',
			user_uuid: '959e3f76-0e5b-4efb-9a6f-4702d864e1ad',
			site_uuid: 'cbf9914d-95d8-4b1f-84b7-be20a85e4edf',
			title: 'Uploaded Image',
			accessibility_text: 'Troy enters the apartment with pizza and sees the fire.',
			type: 'image',
			thumbnail_url: '//cdn.smol.blog/thumb.gif',
			default_url: '//cdn.smol.blog/scene.gif',
			file: '{"id":"bbc80cb4-bf9b-40b5-ad4b-afca7bc79f18","handler":"netflare","details":{"one":"two"},"mimeType":"image\/gif"}',
			uploaded_at: $event->timestamp->format(DateTimeInterface::RFC3339_EXTENDED),
		);
	}

	public function testItWillRetrieveMediaById() {
		$this->db->table('media')->insert([
			'content_uuid' => '52ec4e4d-eecd-4095-a846-aa1139247145',
			'user_uuid' => '06bacbd3-45ed-464a-988b-163dcd4b6018',
			'site_uuid' => '680208fa-76c9-4774-8d1d-589229cc051a',
			'title' => 'But Why',
			'accessibility_text' => 'A young Ryan Reynolds lowers his surgical mask and asks, "But why?"',
			'type' => 'image',
			'thumbnail_url' => '//cdn.smol.blog/thumb.gif',
			'default_url' => '//cdn.smol.blog/scene.gif',
			'file' => '{"id":"139496e8-e6b7-412a-9d6e-08b96dedfc47","handler":"netflare","mimeType":"image/gif","details":{"one":"two"}}',
			'uploaded_at' => '2022-02-02T02:02:02.000T0:00',
		]);
		$expected = new Media(
			id: Identifier::fromString('52ec4e4d-eecd-4095-a846-aa1139247145'),
			userId: Identifier::fromString('06bacbd3-45ed-464a-988b-163dcd4b6018'),
			siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'),
			title: 'But Why',
			accessibilityText: 'A young Ryan Reynolds lowers his surgical mask and asks, "But why?"',
			type: MediaType::Image,
			thumbnailUrl: '//cdn.smol.blog/thumb.gif',
			defaultUrl: '//cdn.smol.blog/scene.gif',
			file: new MediaFile(
				id: Identifier::fromString('139496e8-e6b7-412a-9d6e-08b96dedfc47'),
				handler: 'netflare',
				details: ['one' => 'two'],
				mimeType: 'image/gif',
			),
		);
		$query = new MediaById(
			contentId: Identifier::fromString('52ec4e4d-eecd-4095-a846-aa1139247145'),
			siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'),
		);

		$this->projection->onMediaById($query);

		$this->assertEquals($expected, $query->results());
	}

	public function testItWillApproveVisibilityChecksForMedia() {
		$this->db->table('media')->insert([
			'content_uuid' => '52ec4e4d-eecd-4095-a846-aa1139247145',
			'user_uuid' => '06bacbd3-45ed-464a-988b-163dcd4b6018',
			'site_uuid' => '680208fa-76c9-4774-8d1d-589229cc051a',
			'title' => 'But Why',
			'accessibility_text' => 'A young Ryan Reynolds lowers his surgical mask and asks, "But why?"',
			'type' => 'image',
			'thumbnail_url' => '//cdn.smol.blog/thumb.gif',
			'default_url' => '//cdn.smol.blog/scene.gif',
			'file' => '{"id":"139496e8-e6b7-412a-9d6e-08b96dedfc47","handler":"netflare","mimeType":"image/gif","details":{"one":"two"}}',
			'uploaded_at' => '2022-02-02T02:02:02.000T0:00',
		]);
		$query = new ContentVisibleToUser(
			contentId: Identifier::fromString('52ec4e4d-eecd-4095-a846-aa1139247145'),
			siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'),
			userId: null,
		);

		$this->projection->onContentVisibleToUser($query);

		$this->assertTrue($query->results());
		$this->assertTrue($query->isPropagationStopped());
	}

	public function testItWillIgnoreVisibilityChecksForOtherContent() {
		$this->db->table('media')->insert([
			'content_uuid' => '52ec4e4d-eecd-4095-a846-aa1139247145',
			'user_uuid' => '06bacbd3-45ed-464a-988b-163dcd4b6018',
			'site_uuid' => '680208fa-76c9-4774-8d1d-589229cc051a',
			'title' => 'But Why',
			'accessibility_text' => 'A young Ryan Reynolds lowers his surgical mask and asks, "But why?"',
			'type' => 'image',
			'thumbnail_url' => '//cdn.smol.blog/thumb.gif',
			'default_url' => '//cdn.smol.blog/scene.gif',
			'file' => '{"id":"139496e8-e6b7-412a-9d6e-08b96dedfc47","handler":"netflare","mimeType":"image/gif","details":{"one":"two"}}',
			'uploaded_at' => '2022-02-02T02:02:02.000T0:00',
		]);
		$query = new ContentVisibleToUser(
			contentId: Identifier::fromString('d044e6e1-6bde-44bc-9483-9c94d17315d4'),
			siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'),
			userId: null,
		);

		$this->projection->onContentVisibleToUser($query);

		$this->assertNull($query->results());
		$this->assertFalse($query->isPropagationStopped());
	}

	public function testItWillAddMediaObjectsToMessages() {
		$this->db->table('media')->insert([
			'content_uuid' => '634eee6f-011a-4deb-a5ab-36c67ddb9358',
			'user_uuid' => '06bacbd3-45ed-464a-988b-163dcd4b6018',
			'site_uuid' => '680208fa-76c9-4774-8d1d-589229cc051a',
			'title' => 'But Why',
			'accessibility_text' => 'A young Ryan Reynolds lowers his surgical mask and asks, "But why?"',
			'type' => 'image',
			'thumbnail_url' => '//cdn.smol.blog/thumb.gif',
			'default_url' => '//cdn.smol.blog/scene.gif',
			'file' => '{"id":"139496e8-e6b7-412a-9d6e-08b96dedfc47","handler":"netflare","mimeType":"image/gif","details":{"one":"two"}}',
			'uploaded_at' => '2022-02-02T02:02:02.000T0:00',
		]);
		$this->db->table('media')->insert([
			'content_uuid' => '52ec4e4d-eecd-4095-a846-aa1139247145',
			'user_uuid' => '06bacbd3-45ed-464a-988b-163dcd4b6018',
			'site_uuid' => '680208fa-76c9-4774-8d1d-589229cc051a',
			'title' => 'Screenshot 4412',
			'accessibility_text' => 'A screenshot of a post describing a thing that happened.',
			'type' => 'image',
			'thumbnail_url' => '//cdn.smol.blog/4412_thumb.jpg',
			'default_url' => '//cdn.smol.blog/4412.png',
			'file' => '{"id":"0226a27a-39cb-49a5-9f44-e85815518b03","handler":"netflare","mimeType":"image/png","details":{"one":"two"}}',
			'uploaded_at' => '2022-02-02T02:02:02.000T0:00',
		]);

		$ids = [
			Identifier::fromString('52ec4e4d-eecd-4095-a846-aa1139247145'),
			Identifier::fromString('634eee6f-011a-4deb-a5ab-36c67ddb9358'),
		];
		$expected = [
			new Media(
				id: Identifier::fromString('52ec4e4d-eecd-4095-a846-aa1139247145'),
				userId: Identifier::fromString('06bacbd3-45ed-464a-988b-163dcd4b6018'),
				siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'),
				title: 'Screenshot 4412',
				accessibilityText: 'A screenshot of a post describing a thing that happened.',
				type: MediaType::Image,
				thumbnailUrl: '//cdn.smol.blog/4412_thumb.jpg',
				defaultUrl: '//cdn.smol.blog/4412.png',
				file: new MediaFile(
					id: Identifier::fromString('0226a27a-39cb-49a5-9f44-e85815518b03'),
					handler: 'netflare',
					mimeType: 'image/png',
					details: ['one' => 'two'],
				),
			),
			new Media(
				id: Identifier::fromString('634eee6f-011a-4deb-a5ab-36c67ddb9358'),
				userId: Identifier::fromString('06bacbd3-45ed-464a-988b-163dcd4b6018'),
				siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'),
				title: 'But Why',
				accessibilityText: 'A young Ryan Reynolds lowers his surgical mask and asks, "But why?"',
				type: MediaType::Image,
				thumbnailUrl: '//cdn.smol.blog/thumb.gif',
				defaultUrl: '//cdn.smol.blog/scene.gif',
				file: new MediaFile(
					id: Identifier::fromString('139496e8-e6b7-412a-9d6e-08b96dedfc47'),
					handler: 'netflare',
					mimeType: 'image/gif',
					details: ['one' => 'two'],
				),
			),
		];

		$message = $this->createMock(NeedsMediaObjects::class);
		$message->method('getMediaIds')->willReturn($ids);
		// The order of the media objects should match the order of the given IDs. If these do not match, it is a bug!
		$message->expects($this->once())->method('setMediaObjects')->with($expected);

		$this->projection->onNeedsMediaObjects($message);
	}

	public function testMediaCanBeFoundByDefaultUrl() {
		$this->db->table('media')->insert([
			'content_uuid' => '52ec4e4d-eecd-4095-a846-aa1139247145',
			'user_uuid' => '06bacbd3-45ed-464a-988b-163dcd4b6018',
			'site_uuid' => '680208fa-76c9-4774-8d1d-589229cc051a',
			'title' => 'But Why',
			'accessibility_text' => 'A young Ryan Reynolds lowers his surgical mask and asks, "But why?"',
			'type' => 'image',
			'thumbnail_url' => '//cdn.smol.blog/thumb.gif',
			'default_url' => '//cdn.smol.blog/scene.gif',
			'file' => '{"id":"139496e8-e6b7-412a-9d6e-08b96dedfc47","handler":"netflare","mimeType":"image/gif","details":{"one":"two"}}',
			'uploaded_at' => '2022-02-02T02:02:02.000T0:00',
		]);
		$expected = new Media(
			id: Identifier::fromString('52ec4e4d-eecd-4095-a846-aa1139247145'),
			userId: Identifier::fromString('06bacbd3-45ed-464a-988b-163dcd4b6018'),
			siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'),
			title: 'But Why',
			accessibilityText: 'A young Ryan Reynolds lowers his surgical mask and asks, "But why?"',
			type: MediaType::Image,
			thumbnailUrl: '//cdn.smol.blog/thumb.gif',
			defaultUrl: '//cdn.smol.blog/scene.gif',
			file: new MediaFile(
				id: Identifier::fromString('139496e8-e6b7-412a-9d6e-08b96dedfc47'),
				handler: 'netflare',
				details: ['one' => 'two'],
				mimeType: 'image/gif',
			),
		);
		$query1 = new MediaByDefaultUrl(
			url: '//cdn.smol.blog/scene.gif',
			siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'),
		);

		$query2 = new MediaByDefaultUrl(
			url: '//wrong.url.com/thing.gif',
		);

		$this->projection->onMediaByDefaultUrl($query1);
		$this->projection->onMediaByDefaultUrl($query2);

		$this->assertEquals($expected, $query1->results());
		$this->assertNull($query2->results());
	}

	public function testItWillListAvailableMedia() {
		$this->db->table('media')->insert([
			'content_uuid' => '634eee6f-011a-4deb-a5ab-36c67ddb9358',
			'user_uuid' => '06bacbd3-45ed-464a-988b-163dcd4b6018',
			'site_uuid' => '680208fa-76c9-4774-8d1d-589229cc051a',
			'title' => 'But Why',
			'accessibility_text' => 'A young Ryan Reynolds lowers his surgical mask and asks, "But why?"',
			'type' => 'image',
			'thumbnail_url' => '//cdn.smol.blog/thumb.gif',
			'default_url' => '//cdn.smol.blog/scene.gif',
			'file' => '{"id":"139496e8-e6b7-412a-9d6e-08b96dedfc47","handler":"netflare","mimeType":"image/gif","details":{"one":"two"}}',
			'uploaded_at' => '2022-02-02T02:02:01.000T0:00',
		]);
		$this->db->table('media')->insert([
			'content_uuid' => '52ec4e4d-eecd-4095-a846-aa1139247145',
			'user_uuid' => '06bacbd3-45ed-464a-988b-163dcd4b6018',
			'site_uuid' => '680208fa-76c9-4774-8d1d-589229cc051a',
			'title' => 'Screenshot 4412',
			'accessibility_text' => 'A screenshot of a post describing a thing that happened.',
			'type' => 'image',
			'thumbnail_url' => '//cdn.smol.blog/4412_thumb.jpg',
			'default_url' => '//cdn.smol.blog/4412.png',
			'file' => '{"id":"0226a27a-39cb-49a5-9f44-e85815518b03","handler":"netflare","mimeType":"image/png","details":{"one":"two"}}',
			'uploaded_at' => '2022-02-02T02:02:02.000T0:00',
		]);

		$expected = [
			new Media(
				id: Identifier::fromString('52ec4e4d-eecd-4095-a846-aa1139247145'),
				userId: Identifier::fromString('06bacbd3-45ed-464a-988b-163dcd4b6018'),
				siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'),
				title: 'Screenshot 4412',
				accessibilityText: 'A screenshot of a post describing a thing that happened.',
				type: MediaType::Image,
				thumbnailUrl: '//cdn.smol.blog/4412_thumb.jpg',
				defaultUrl: '//cdn.smol.blog/4412.png',
				file: new MediaFile(
					id: Identifier::fromString('0226a27a-39cb-49a5-9f44-e85815518b03'),
					handler: 'netflare',
					mimeType: 'image/png',
					details: ['one' => 'two'],
				),
			),
			new Media(
				id: Identifier::fromString('634eee6f-011a-4deb-a5ab-36c67ddb9358'),
				userId: Identifier::fromString('06bacbd3-45ed-464a-988b-163dcd4b6018'),
				siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'),
				title: 'But Why',
				accessibilityText: 'A young Ryan Reynolds lowers his surgical mask and asks, "But why?"',
				type: MediaType::Image,
				thumbnailUrl: '//cdn.smol.blog/thumb.gif',
				defaultUrl: '//cdn.smol.blog/scene.gif',
				file: new MediaFile(
					id: Identifier::fromString('139496e8-e6b7-412a-9d6e-08b96dedfc47'),
					handler: 'netflare',
					mimeType: 'image/gif',
					details: ['one' => 'two'],
				),
			),
		];

		$query = new MediaList(siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'));
		$this->projection->onMediaList($query);
		$this->assertEquals($expected, $query->results());
		$this->assertEquals(2, $query->count);
	}

	public function testTheMediaListCanBeFilteredByType() {
		$this->db->table('media')->insert([
			'content_uuid' => '634eee6f-011a-4deb-a5ab-36c67ddb9358',
			'user_uuid' => '06bacbd3-45ed-464a-988b-163dcd4b6018',
			'site_uuid' => '680208fa-76c9-4774-8d1d-589229cc051a',
			'title' => 'But Why',
			'accessibility_text' => 'A young Ryan Reynolds lowers his surgical mask and asks, "But why?"',
			'type' => 'image',
			'thumbnail_url' => '//cdn.smol.blog/thumb.gif',
			'default_url' => '//cdn.smol.blog/scene.gif',
			'file' => '{"id":"139496e8-e6b7-412a-9d6e-08b96dedfc47","handler":"netflare","mimeType":"image/gif","details":{"one":"two"}}',
			'uploaded_at' => '2022-02-02T02:02:01.000T0:00',
		]);
		$this->db->table('media')->insert([
			'content_uuid' => '52ec4e4d-eecd-4095-a846-aa1139247145',
			'user_uuid' => '06bacbd3-45ed-464a-988b-163dcd4b6018',
			'site_uuid' => '680208fa-76c9-4774-8d1d-589229cc051a',
			'title' => 'Screenshot 4412',
			'accessibility_text' => 'A screenshot of a post describing a thing that happened.',
			'type' => 'video',
			'thumbnail_url' => '//cdn.smol.blog/4412_thumb.jpg',
			'default_url' => '//cdn.smol.blog/4412.png',
			'file' => '{"id":"0226a27a-39cb-49a5-9f44-e85815518b03","handler":"netflare","mimeType":"video/mpeg","details":{"one":"two"}}',
			'uploaded_at' => '2022-02-02T02:02:02.000T0:00',
		]);

		$expected = [
			new Media(
				id: Identifier::fromString('52ec4e4d-eecd-4095-a846-aa1139247145'),
				userId: Identifier::fromString('06bacbd3-45ed-464a-988b-163dcd4b6018'),
				siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'),
				title: 'Screenshot 4412',
				accessibilityText: 'A screenshot of a post describing a thing that happened.',
				type: MediaType::Video,
				thumbnailUrl: '//cdn.smol.blog/4412_thumb.jpg',
				defaultUrl: '//cdn.smol.blog/4412.png',
				file: new MediaFile(
					id: Identifier::fromString('0226a27a-39cb-49a5-9f44-e85815518b03'),
					handler: 'netflare',
					mimeType: 'video/mpeg',
					details: ['one' => 'two'],
				),
			),
		];

		$query = new MediaList(
			siteId: Identifier::fromString('680208fa-76c9-4774-8d1d-589229cc051a'),
			types: [MediaType::Video]
		);
		$this->projection->onMediaList($query);
		$this->assertEquals($expected, $query->results());
		$this->assertEquals(1, $query->count);
	}
}
