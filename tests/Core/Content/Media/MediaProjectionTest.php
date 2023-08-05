<?php

namespace Smolblog\Core\Content\Media;

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
}
