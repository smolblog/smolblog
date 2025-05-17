<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Core\Test\ContentTypeTest;

final class PictureTest extends ContentTypeTest {
	const string TYPE_KEY = 'picture';
	const string SERVICE_CLASS = PictureService::class;
	const string TYPE_CLASS = Picture::class;

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
		$this->assertEquals('Something wistful.', $actual->getTitle());
	}

	public function testItUsesTheFirstImageTitleIfNoneGiven() {
		$actual = $this->createExampleType()->with(caption: null);
		$this->assertEquals('Title.jpg', $actual->getTitle());
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
