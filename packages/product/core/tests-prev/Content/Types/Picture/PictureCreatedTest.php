<?php

namespace Smolblog\Core\Content\Types\Picture;

use DateTimeImmutable;
use Smolblog\Core\Content\Events\ContentEvent;
use Smolblog\Core\Content\Media\Media;
use Smolblog\Core\Content\Media\MediaFile;
use Smolblog\Core\Content\Media\MediaType;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\Kits\ContentEventTestKit;
use Smolblog\Test\Kits\NeedsMarkdownRenderedTestKit;
use Smolblog\Test\Kits\NeedsMediaObjectsTestKit;
use Smolblog\Test\Kits\NeedsMediaRenderedTestKit;
use Smolblog\Test\TestCase;

final class PictureCreatedTest extends TestCase {
	use NeedsMediaObjectsTestKit;
	use NeedsMediaRenderedTestKit;
	use NeedsMarkdownRenderedTestKit;
	use ContentEventTestKit;

	protected function setUp(): void
	{
		$this->subject = new PictureCreated(
			mediaIds: [
				Identifier::fromString('ec1c329d-7b24-4a1b-907b-74213412f0e3'),
				Identifier::fromString('5b75dee6-3909-4397-82c0-cce1aaeadfa9'),
				Identifier::fromString('955de54b-6d61-427d-8135-f622e4af7596'),
			],
			authorId: $this->randomId(true),
			contentId: $this->randomId(true),
			userId: $this->randomId(true),
			siteId: $this->randomId(true),
			caption: 'Well then.',
			publishTimestamp: new DateTimeImmutable('2022-02-02 22:22:22.222'),
			id: $this->randomId(true),
			timestamp: new DateTimeImmutable('2011-11-11 11:11:11.111'),
		);
	}

	public function testItPullsTheTitleFromTheCaption() {
		$this->assertEquals('Well then.', $this->subject->getNewTitle());
	}

	public function testItPullsTheTitleFromTheMediaIfThereIsNoCaption() {
		$titleless = new PictureCreated(
			mediaIds: [Identifier::fromString('e24604cb-74a7-46ee-b53d-4ad61093a0ee')],
			authorId: $this->randomId(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);
		$titleless->setMediaObjects([new Media(
			id: Identifier::fromString('e24604cb-74a7-46ee-b53d-4ad61093a0ee'),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'That so.',
			accessibilityText: 'Really.',
			type: MediaType::Image,
			thumbnailUrl: '//smol.blog/thumb.jpg',
			defaultUrl: '//smol.blog/img.png',
			file: $this->createStub(MediaFile::class),
		)]);
		$this->assertEquals('That so.', $titleless->getNewTitle());
	}

	public function testItCreatesPictures() {
		$this->assertEquals('picture', $this->subject->getContentType());
	}

	public function testItWillStripArrayKeys() {
		$event = new PictureCreated(
			mediaIds: [
				'photo' => Identifier::fromString('d2655068-427e-40e3-bbd4-83154da2d443'),
				'image' => Identifier::fromString('9fcfde4b-63f6-41a1-a521-d2bcd7e50ffb'),
			],
			authorId: $this->randomId(true),
			contentId: $this->randomId(true),
			userId: $this->randomId(true),
			siteId: $this->randomId(true),
			caption: 'Well then.',
			publishTimestamp: new DateTimeImmutable('2022-02-02 22:22:22.222'),
			id: $this->randomId(true),
			timestamp: new DateTimeImmutable('2011-11-11 11:11:11.111'),
		);

		$this->assertEquals([
			Identifier::fromString('d2655068-427e-40e3-bbd4-83154da2d443'),
			Identifier::fromString('9fcfde4b-63f6-41a1-a521-d2bcd7e50ffb'),
		], $event->mediaIds);
	}
}
