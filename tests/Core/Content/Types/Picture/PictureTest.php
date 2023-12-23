<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\InvalidContentException;
use Smolblog\Core\Content\Media\Media;
use Smolblog\Core\Content\Media\MediaFile;
use Smolblog\Core\Content\Media\MediaTest;
use Smolblog\Core\Content\Media\MediaType;
use Smolblog\Test\SerializableTestKit;
use Smolblog\Test\TestCase;

final class PictureTest extends TestCase {
	use SerializableTestKit;

	private array $mediaList;
	protected function setUp(): void
	{
		$this->mediaList = [
			new Media(
				id: $this->randomId(true),
				userId: $this->randomId(true),
				siteId: $this->randomId(true),
				title: 'One',
				accessibilityText: 'One',
				type: MediaType::Image,
				thumbnailUrl: '//.jpg',
				defaultUrl: '//.gif',
				file: new MediaFile(
					id: $this->randomId(true),
					handler: 'wp',
					details: ['one' => 'two'],
					mimeType: 'picture/jpeg',
				)
			),
			new Media(
				id: $this->randomId(true),
				userId: $this->randomId(true),
				siteId: $this->randomId(true),
				title: 'Two',
				accessibilityText: 'Two',
				type: MediaType::Image,
				thumbnailUrl: '//.jpg',
				defaultUrl: '//.gif',
				file: new MediaFile(
					id: $this->randomId(true),
					handler: 'wp',
					details: ['one' => 'two'],
					mimeType: 'picture/jpeg',
				)
			),
		];

		$this->subject = new Picture(
			media: $this->mediaList,
			caption: 'Now I **really** mean it.',
		);
	}

	public function testItRequiresANonemptyArray() {
		$this->expectException(InvalidContentException::class);

		new Picture(media: []);
	}

	public function testItOnlyAllowsImageMedia() {
		$this->expectException(InvalidContentException::class);

		new Picture([
			new Media(
				id: $this->randomId(),
				userId: $this->randomId(),
				siteId: $this->randomId(),
				title: 'Two',
				accessibilityText: 'Two',
				type: MediaType::Video,
				thumbnailUrl: '//.jpg',
				defaultUrl: '//.gif',
				file: $this->createStub(MediaFile::class)
			),
		]);
	}

	public function testTheTitleIsDerivedFromTheCaptionOrMedia() {
		$this->assertEquals(
			'One',
			(new Picture($this->mediaList))->getTitle(),
		);
		$this->assertEquals(
			'Something happened.',
			(new Picture(media: $this->mediaList, caption: 'Something happened.'))->getTitle(),
		);
	}

	public function testItUsesProvidedValuesToCreateTheBody() {
		$this->assertEquals(
			'<img src="one">' . "\n\n" . '<img src="two">' . "\n\n",
			(new Picture(media: $this->mediaList, mediaHtml: ['<img src="one">', '<img src="two">']))->getBodyContent(),
		);
		$this->assertEquals(
			'<img src="one">' . "\n\n" . '<img src="two">' . "\n\n<p>Something.</p>",
			(new Picture(
				media: $this->mediaList,
				mediaHtml: ['<img src="one">', '<img src="two">'],
				caption: 'Soemthing.',
				captionHtml: '<p>Something.</p>',
			))->getBodyContent(),
		);
	}

	public function testItsTypeKeyIsPicture() {
		$this->assertEquals('picture', (new Picture($this->mediaList))->getTypeKey());
	}
}
