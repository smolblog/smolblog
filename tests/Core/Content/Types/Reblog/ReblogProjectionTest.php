<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Illuminate\Database\Schema\Blueprint;
use Smolblog\Test\DatabaseTestKit;
use Smolblog\Test\TestCase;

final class ReblogProjectionTest extends TestCase {
	use DatabaseTestKit;

	private ReblogProjection $projection;

	public function setUp(): void {
		$this->initDatabaseWithTable('reblogs', function(Blueprint $table) {
			$table->uuid('content_uuid')->primary();
			$table->string('url');
			$table->text('comment');
			$table->text('comment_html');
			$table->text('url_info');
		});

		$this->projection = new ReblogProjection(db: $this->db);
	}

	public function setUpSampleRow() {
		$contentId = $this->randomId();
		$this->db->table('reblogs')->insert([
			'content_uuid' => $contentId->toString(),
			'url' => 'https://youtu.be/rTga41r3a4s',
			'url_info' => '{"title":"Rick Astley - Never Gonna Give You Up (Pianoforte) (Performance)","embed":"<iframe src=\"https:\/\/www.youtube.com\/embed\/rTga41r3a4s\" allowfullscreen><\/iframe>"}',
			'comment' => 'But *why?*',
			'comment_html' => '<p>But <em>why?</em></p>',
		]);

		return $contentId;
	}

	public function testItWillAddANewReblog() {
		$event = new ReblogCreated(
			url: 'https://youtu.be/rTga41r3a4s',
			comment: 'But *why?*',
			info: new ExternalContentInfo(
				title: 'Rick Astley - Never Gonna Give You Up (Pianoforte) (Performance)',
				embed: '<iframe src="https://www.youtube.com/embed/rTga41r3a4s" allowfullscreen></iframe>',
			),
			authorId: $this->randomId(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);
		$event->setHtml('<p>But <em>why?</em></p>');

		$this->projection->onReblogCreated($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('reblogs'),
			content_uuid: $event->contentId->toString(),
			url: 'https://youtu.be/rTga41r3a4s',
			url_info: '{"title":"Rick Astley - Never Gonna Give You Up (Pianoforte) (Performance)","embed":"<iframe src=\"https:\/\/www.youtube.com\/embed\/rTga41r3a4s\" allowfullscreen><\/iframe>"}',
			comment: 'But *why?*',
			comment_html: '<p>But <em>why?</em></p>',
		);
	}

	public function testItWillUpdateTheCommentForAnExistingReblog() {
		$contentId = $this->setUpSampleRow();

		$event = new ReblogCommentChanged(
			comment: 'Seriously?',
			contentId: $contentId,
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);
		$event->setHtml('<p>Seriously?</p>');

		$this->projection->onReblogCommentChanged($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('reblogs'),
			content_uuid: $event->contentId->toString(),
			url: 'https://youtu.be/rTga41r3a4s',
			url_info: '{"title":"Rick Astley - Never Gonna Give You Up (Pianoforte) (Performance)","embed":"<iframe src=\"https:\/\/www.youtube.com\/embed\/rTga41r3a4s\" allowfullscreen><\/iframe>"}',
			comment: 'Seriously?',
			comment_html: '<p>Seriously?</p>',
		);
	}

	public function testItWillUpdateTheInfoForAnExistingReblog() {
		$contentId = $this->setUpSampleRow();

		$event = new ReblogInfoChanged(
			url: 'https://youtu.be/dJRsWJqDjFE',
			info: new ExternalContentInfo(
				title: 'Choir! Choir! Choir! / Rick Astley - Never Gonna Give You Up!!!',
				embed: '<iframe src="https://www.youtube.com/embed/dJRsWJqDjFE" allowfullscreen></iframe>',
			),
			contentId: $contentId,
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$this->projection->onReblogInfoChanged($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('reblogs'),
			content_uuid: $event->contentId->toString(),
			url: 'https://youtu.be/dJRsWJqDjFE',
			url_info: '{"title":"Choir! Choir! Choir! \/ Rick Astley - Never Gonna Give You Up!!!","embed":"<iframe src=\"https:\/\/www.youtube.com\/embed\/dJRsWJqDjFE\" allowfullscreen><\/iframe>"}',
			comment: 'But *why?*',
			comment_html: '<p>But <em>why?</em></p>',
		);
	}

	public function testItWillDeleteAReblog() {
		$contentId = $this->setUpSampleRow();

		$event = new ReblogDeleted(
			contentId: $contentId,
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$this->projection->onReblogDeleted($event);

		$this->assertTableEmpty($this->db->table('reblogs'));
	}

	public function testItWillAddReblogDataToAReblogBuilder() {
		$contentId = $this->setUpSampleRow();

		$message = $this->createMock(ReblogBuilder::class);
		$message->method('getContentId')->willReturn($contentId);
		$message->expects($this->once())->method('setContentType')->with($this->equalTo(
			new Reblog(
				url: 'https://youtu.be/rTga41r3a4s',
				info: new ExternalContentInfo(
					title: 'Rick Astley - Never Gonna Give You Up (Pianoforte) (Performance)',
					embed: '<iframe src="https://www.youtube.com/embed/rTga41r3a4s" allowfullscreen></iframe>',
				),
				comment: 'But *why?*',
				commentHtml: '<p>But <em>why?</em></p>',
			)
		));

		$this->projection->buildReblog($message);
	}
}