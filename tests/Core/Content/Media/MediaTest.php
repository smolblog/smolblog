<?php

namespace Smolblog\Core\Content\Media;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\TestCase;

final class MediaTest extends TestCase {
	public function testItCanBeSerializedAndDeserialized() {
		$object = new Media(
			id: Identifier::fromString('a96a358f-f730-45cf-a78a-779af98adfd6'),
			userId: Identifier::fromString('a0ca8f07-3cc5-4ffb-b1ef-526c0b6de4e2'),
			siteId: Identifier::fromString('0a56b693-9721-46f0-bfd7-15e6a42495e6'),
			title: 'IMG_90108.jpg',
			accessibilityText: 'Some creek I guess',
			type: MediaType::Image,
			thumbnailUrl: '//img/thumb.jpg',
			defaultUrl: '//img/orig.jpg',
			defaultHtml: '<img src="orig.jpg">',
			file: new MediaFile(
				id: Identifier::fromString('55d7d2e5-107d-4c35-9118-453f5973a88b'),
				handler: 'default',
				mimeType: 'image/jpeg',
				details: ['one' => 'two'],
			),
		);
		$array = [
			'id' => 'a96a358f-f730-45cf-a78a-779af98adfd6',
			'userId' => 'a0ca8f07-3cc5-4ffb-b1ef-526c0b6de4e2',
			'siteId' => '0a56b693-9721-46f0-bfd7-15e6a42495e6',
			'title' => 'IMG_90108.jpg',
			'accessibilityText' => 'Some creek I guess',
			'type' => 'image',
			'thumbnailUrl' => '//img/thumb.jpg',
			'defaultUrl' => '//img/orig.jpg',
			'defaultHtml' => '<img src="orig.jpg">',
			'file' => [
				'id' => '55d7d2e5-107d-4c35-9118-453f5973a88b',
				'handler' => 'default',
				'mimeType' => 'image/jpeg',
				'details' => ['one' => 'two'],
			],
		];

		$this->assertEquals($object, Media::fromArray($array));
		$this->assertEquals($array, $object->toArray());
	}
}
