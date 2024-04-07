<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Illuminate\Database\Schema\Blueprint;
use Smolblog\Core\ContentV1\Media\Media;
use Smolblog\Core\ContentV1\Media\MediaFile;
use Smolblog\Core\ContentV1\Media\MediaType;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\Kits\DatabaseTestKit;
use Smolblog\Test\TestCase;

final class PictureProjectionTest extends TestCase {
	use DatabaseTestKit;

	private PictureProjection $projection;
	private array $mediaList;
	private array $mediaHtml;

	public function setUp(): void {
		$this->initDatabaseWithTable('pictures', function(Blueprint $table) {
			$table->uuid('content_uuid')->primary();
			$table->text('media');
			$table->text('caption')->nullable();
			$table->string('media_html')->nullable();
			$table->text('caption_html')->nullable();
		});

		$this->projection = new PictureProjection(db: $this->db);
		$this->mediaList = [
			new Media(
				id: Identifier::fromString('229be2e6-e157-4a84-9125-59ba77346090'),
				userId: $this->randomId(true),
				siteId: $this->randomId(true),
				title: 'One',
				accessibilityText: 'One',
				type: MediaType::Image,
				thumbnailUrl: '//.jpg',
				defaultUrl: '//.gif',
				file: new MediaFile(
					id: $this->randomId(true),
					handler: 'somewhere',
					details: ['one' => 'two'],
				)
			),
			new Media(
				id: Identifier::fromString('0c0d8084-94d5-4688-a116-4354a546796f'),
				userId: $this->randomId(true),
				siteId: $this->randomId(true),
				title: 'Two',
				accessibilityText: 'Two',
				type: MediaType::Image,
				thumbnailUrl: '//.jpg',
				defaultUrl: '//.gif',
				file: new MediaFile(
					id: $this->randomId(true),
					handler: 'somewhere',
					details: ['one' => 'two'],
				)
			),
		];
		$this->mediaHtml = [
			'<img src="one">',
			'<img src="two">',
		];
	}

	public function setUpSampleRow() {
		$contentId = $this->randomId();
		$this->db->table('pictures')->insert([
			'content_uuid' => $contentId->toString(),
			'media' => json_encode($this->mediaList),
			'caption' => 'Something.',
			'media_html' => json_encode($this->mediaHtml),
			'caption_html' => '<p>Something.</p>',
		]);

		return $contentId;
	}

	public function testItWillAddANewPicture() {
		$event = new PictureCreated(
			mediaIds: [
				Identifier::fromString('229be2e6-e157-4a84-9125-59ba77346090'),
				Identifier::fromString('0c0d8084-94d5-4688-a116-4354a546796f'),
			],
			authorId: $this->randomId(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			caption: 'Hello there.'
		);
		$event->setMediaObjects($this->mediaList);
		$event->setMediaHtml($this->mediaHtml);
		$event->setMarkdownHtml(['<p>Hello there.</p>']);

		$this->projection->onPictureCreated($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('pictures'),
			content_uuid: $event->contentId->toString(),
			media: json_encode($this->mediaList),
			caption: 'Hello there.',
			media_html: json_encode($this->mediaHtml),
			caption_html: '<p>Hello there.</p>',
		);
		$this->assertEquals('Hello there.', $event->getNewTitle());
		$this->assertEquals(
			'<img src="one">' . "\n\n" . '<img src="two">' . "\n\n" . '<p>Hello there.</p>',
			$event->getNewBody(),
		);
	}

	public function testItWillUpdateTheMediaForAnExistingPicture() {
		$contentId = $this->setUpSampleRow();
		$this->db->table('pictures')->where(['content_uuid' => $contentId])->update(['caption' => null]);

		$newMedia = new Media(
			id: $this->randomId(true),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'Three',
			accessibilityText: 'Three',
			type: MediaType::Image,
			thumbnailUrl: '//thumb.jpg',
			defaultUrl: '//three.png',
			file: new MediaFile(
				id: $this->randomId(),
				handler: 'someone',
				details: ['one'=>'two'],
			),
		);

		$event = new PictureMediaEdited(
			mediaIds: [
				Identifier::fromString('229be2e6-e157-4a84-9125-59ba77346090'),
				Identifier::fromString('0c0d8084-94d5-4688-a116-4354a546796f'),
				$newMedia->id,
			],
			contentId: $contentId,
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);
		$event->setMediaObjects([...$this->mediaList, $newMedia]);
		$event->setMediaHtml([...$this->mediaHtml, '<img src="three">']);

		$this->projection->onPictureMediaEdited($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('pictures'),
			content_uuid: $event->contentId->toString(),
			media: json_encode([...$this->mediaList, $newMedia]),
			media_html: json_encode([...$this->mediaHtml, '<img src="three">']),
			caption_html: '<p>Something.</p>',
			caption: null,
		);
		$this->assertEquals('One', $event->getNewTitle());
		$this->assertEquals(
			'<img src="one">' . "\n\n" . '<img src="two">' . "\n\n" . '<img src="three">' . "\n\n" . '<p>Something.</p>',
			$event->getNewBody()
		);
	}

	public function testItWillUpdateTheCaptionForAnExistingPicture() {
		$contentId = $this->setUpSampleRow();

		$event = new PictureCaptionEdited(
			caption: 'Seriously.',
			contentId: $contentId,
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);
		$event->setMarkdownHtml(['<p>Seriously.</p>']);

		$this->projection->onPictureCaptionEdited($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('pictures'),
			content_uuid: $event->contentId->toString(),
			media: json_encode($this->mediaList),
			caption: 'Seriously.',
			media_html: json_encode($this->mediaHtml),
			caption_html: '<p>Seriously.</p>',
		);
		$this->assertEquals('Seriously.', $event->getNewTitle());
		$this->assertEquals(
			'<img src="one">' . "\n\n" . '<img src="two">' . "\n\n" . '<p>Seriously.</p>',
			$event->getNewBody()
		);
	}

	public function testItWillDeleteAPicture() {
		$contentId = $this->setUpSampleRow();

		$event = new PictureDeleted(
			contentId: $contentId,
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$this->projection->onPictureDeleted($event);

		$this->assertTableEmpty($this->db->table('pictures'));
	}

	public function testItWillAddPictureDataToAPictureBuilder() {
		$contentId = $this->setUpSampleRow();

		$message = $this->createMock(PictureBuilder::class);
		$message->method('getContentId')->willReturn($contentId);
		$message->expects($this->once())->method('setContentType')->with($this->equalTo(
			new Picture(
				media: $this->mediaList,
				caption: 'Something.',
				mediaHtml: $this->mediaHtml,
				captionHtml: '<p>Something.</p>',
			)
		));

		$this->projection->buildPicture($message);
	}
}
