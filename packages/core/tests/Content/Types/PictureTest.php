<?php

namespace Smolblog\Core\Content\Types\Picture;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Factories\UuidFactory;
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

	protected function createExampleType(): ContentType {
		return new Picture(pictures: [
			UuidFactory::fromString('5203ceba-fbfd-45fb-8ee7-3b35a8ae991b'),
			UuidFactory::fromString('beb0e745-3fca-4f36-8486-6241413db05a'),
		], caption: new Markdown('This is _only_ a test.'));
	}

	protected function createModifiedType(): ContentType {
		return new Picture(pictures: [
			UuidFactory::fromString('5203ceba-fbfd-45fb-8ee7-3b35a8ae991b'),
			UuidFactory::fromString('beb0e745-3fca-4f36-8486-6241413db05a'),
		], caption: new Markdown('This is **only** a test.'));
	}

	public function testItUsesTheCaptionIfGivenForTheTitle() {
		$actual = $this->createExampleType()->with(caption: new Markdown('Something wistful.'));
		$this->assertEquals('Something wistful.', $actual->title);
	}

	public function testItUsesADefaultTitleIfNoneGiven() {
		$actual = $this->createExampleType()->with(caption: null);
		$this->assertNotEmpty($actual->title);
	}

	public function testPicturesCannotBeEmpty() {
		$this->expectException(InvalidValueProperties::class);

		new Picture(pictures: []);
	}

	public function testPicturesMustOnlyContainMedia() {
		$this->expectException(InvalidValueProperties::class);

		new Picture(pictures: [
			$this->randomId(),
			'something',
		]);
	}
}
