<?php

namespace Smolblog\Core\Content\Media;

use DateTimeImmutable;
use Smolblog\Core\Content\Events\ContentEvent;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\TestCase;

final class MediaAddedTest extends TestCase {
	public function testItSerializesAndDeserializesCorrectly() {
		$object = new MediaAdded(
			contentId: Identifier::fromString('e7db9681-8f7f-43bb-8071-dbca16eb5da4'),
			userId: Identifier::fromString('76c83829-be0e-4c1f-a89c-3e096fab0918'),
			siteId: Identifier::fromString('2e5326bf-9975-4688-a664-4efe8892ef46'),
			title: 'Test Event',
			accessibilityText: 'Unknown',
			type: MediaType::Audio,
			thumbnailUrl: '//media/thing.png',
			defaultUrl: '//media/thing.ogg',
			file: new MediaFile(
				id: Identifier::fromString('fc9b6791-0bfa-48cf-baa1-849ad4d4d8f0'),
				handler: 'default',
				details: ['one' => 'two'],
				mimeType: 'audio/ogg',
			),
			id: Identifier::fromString('c17e8e42-8178-43bf-96cc-ca85f4e7c35d'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22+0:00'),
		);

		$array = [
			'type' => MediaAdded::class,
			'contentId' => 'e7db9681-8f7f-43bb-8071-dbca16eb5da4',
			'userId' => '76c83829-be0e-4c1f-a89c-3e096fab0918',
			'siteId' => '2e5326bf-9975-4688-a664-4efe8892ef46',
			'id' => 'c17e8e42-8178-43bf-96cc-ca85f4e7c35d',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'title' => 'Test Event',
				'accessibilityText' => 'Unknown',
				'type' => 'audio',
				'thumbnailUrl' => '//media/thing.png',
				'defaultUrl' => '//media/thing.ogg',
				'file' => [
					'id' => 'fc9b6791-0bfa-48cf-baa1-849ad4d4d8f0',
					'handler' => 'default',
					'details' => ['one' => 'two'],
					'mimeType' => 'audio/ogg',
				],
			]
		];

		$this->assertEquals($array, $object->toArray());
		$this->assertEquals($object, ContentEvent::fromTypedArray($array));
	}
}
