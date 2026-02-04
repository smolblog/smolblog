<?php

namespace Smolblog\Core\Content\Types\Picture;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Fields\Markdown;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Test\ContentTypeTest;

#[AllowMockObjectsWithoutExpectations]
final class PictureTest extends ContentTypeTest {
	public const string TYPE_KEY = 'picture';
	public const string SERVICE_CLASS = PictureService::class;
	public const string TYPE_CLASS = Picture::class;

	protected const CREATE_EVENT = PictureCreated::class;
	protected const UPDATE_EVENT = PictureUpdated::class;
	protected const DELETE_EVENT = PictureDeleted::class;

	private function makeMedia(): Media {
		return new Media(
			id: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'Title.jpg',
			accessibilityText: 'Troy returning with several pizzas.',
			type: MediaType::Image,
			handler: 'test',
			fileDetails: [],
		);
	}

	protected function createExampleType(): ContentType {
		return new Picture(pictures: [
			$this->makeMedia(),
			$this->makeMedia()->with(type: MediaType::Video),
		], caption: new Markdown('This is _only_ a test.'));
	}

	protected function createModifiedType(): ContentType {
		return new Picture(pictures: [
			$this->makeMedia(),
			$this->makeMedia()->with(type: MediaType::Video),
		], caption: new Markdown('This is **only** a test.'));
	}

	public function testItUsesTheCaptionIfGivenForTheTitle() {
		$actual = $this->createExampleType()->with(caption: new Markdown('Something wistful.'));
		$this->assertEquals('Something wistful.', $actual->title);
	}

	public function testItUsesTheFirstImageTitleIfNoneGiven() {
		$actual = $this->createExampleType()->with(caption: null);
		$this->assertEquals('Title.jpg', $actual->title);
	}

	public function testPicturesCannotBeEmpty() {
		$this->expectException(InvalidValueProperties::class);

		new Picture(pictures: []);
	}

	public function testPicturesMustOnlyContainMedia() {
		$this->expectException(InvalidValueProperties::class);

		new Picture(pictures: [
			$this->makeMedia(),
			'something',
		]);
	}

	public function testPicturesMustOnlyContainPictureOrVideoMedia() {
		$this->expectException(InvalidValueProperties::class);

		new Picture(pictures: [
			$this->makeMedia(),
			$this->makeMedia()->with(type: MediaType::File),
			$this->makeMedia(),
		]);
	}
}
