<?php

namespace Smolblog\Core\ContentV1\Media;

use DateTimeImmutable;
use Smolblog\Core\ContentV1\Events\ContentEvent;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\TestCase;

final class MediaFileAddedTest extends TestCase {
	public function testItSerializesAndDeserializesCorrectly() {
		$object = new MediaFileAdded(
			contentId: Identifier::fromString('e7db9681-8f7f-43bb-8071-dbca16eb5da4'),
			userId: Identifier::fromString('76c83829-be0e-4c1f-a89c-3e096fab0918'),
			siteId: Identifier::fromString('2e5326bf-9975-4688-a664-4efe8892ef46'),
			handler: 'default',
			mimeType: 'image/jpeg',
			details: ['one' => 'two'],
			id: Identifier::fromString('c17e8e42-8178-43bf-96cc-ca85f4e7c35d'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22+0:00'),
		);

		$array = [
			'type' => MediaFileAdded::class,
			'contentId' => 'e7db9681-8f7f-43bb-8071-dbca16eb5da4',
			'userId' => '76c83829-be0e-4c1f-a89c-3e096fab0918',
			'siteId' => '2e5326bf-9975-4688-a664-4efe8892ef46',
			'id' => 'c17e8e42-8178-43bf-96cc-ca85f4e7c35d',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'mimeType' => 'image/jpeg',
				'handler' => 'default',
				'details' => ['one' => 'two'],
			]
		];

		$this->assertEquals($array, $object->toArray());
		$this->assertEquals($object, ContentEvent::fromTypedArray($array));
	}
}
