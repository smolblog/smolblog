<?php

namespace Smolblog\Core\Content\Extensions\Syndication;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Content\Events\ContentEvent;
use Smolblog\Framework\Objects\Identifier;

final class SyndicationChannelsSetTest extends TestCase {
	public function testItWillSerializeAndDeserializeCorrectly() {
		$object = new SyndicationChannelsSet(
			channels: [
				Identifier::fromString('8802b19a-a925-46d8-bac6-f75a6e8d4d74'),
				Identifier::fromString('eeaacf37-0d38-4026-997d-6288c6ec0e7c'),
			],
			userId: Identifier::fromString('34644995-b4a7-4723-9a84-a81254836c36'),
			siteId: Identifier::fromString('24482c2b-9726-49bf-ae87-f76542eecdd0'),
			contentId: Identifier::fromString('e29bdb12-c87c-4625-addd-f9c0ed296474'),
			id: Identifier::fromString('05ef2052-a910-4226-8fff-a615a7f1875c'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22.222'),
		);

		$array = [
			'type' => SyndicationChannelsSet::class,
			'userId' => '34644995-b4a7-4723-9a84-a81254836c36',
			'siteId' => '24482c2b-9726-49bf-ae87-f76542eecdd0',
			'contentId' => 'e29bdb12-c87c-4625-addd-f9c0ed296474',
			'id' => '05ef2052-a910-4226-8fff-a615a7f1875c',
			'timestamp' => '2022-02-22T22:22:22.222+00:00',
			'payload' => [
				'channels' => [
					'8802b19a-a925-46d8-bac6-f75a6e8d4d74',
					'eeaacf37-0d38-4026-997d-6288c6ec0e7c',
				],
			],
		];

		$this->assertEquals($object, ContentEvent::fromTypedArray($array));
		$this->assertEquals($array, $object->toArray());
	}

	public function testTheExtensionCanBeSetAndRetrieved() {
		$event = new SyndicationChannelsSet(
			channels: [],
			contentId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			siteId: Identifier::createRandom(),
		);
		$state = new Syndication(
			links: ['https://smol.blog/1254'],
			channels: [Identifier::createRandom()],
		);

		$event->setState($state);
		$this->assertEquals($state, $event->getNewExtension());
	}
}
