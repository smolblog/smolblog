<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Core\ContentV1\Events\ContentEvent;
use Smolblog\Foundation\Value\Fields\Identifier;

final class ReblogInfoChangedTest extends TestCase {
	public function testItWillDeserializeCorrectly() {
		$expected = new ReblogInfoChanged(
			url: '//smol.blog/',
			info: new ExternalContentInfo(title: 'No strangers', embed: '<iframe></iframe>'),
			contentId: Identifier::fromString('536ef534-6820-4dba-b857-9e81792bdd5e'),
			userId: Identifier::fromString('32eba133-c034-4575-a70d-37e2364c3bc6'),
			siteId: Identifier::fromString('d177f415-52bf-4726-a013-3e03767aa619'),
			id: Identifier::fromString('c8247b5a-5dd5-4d43-af48-61000bbc0bc1'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);

		$actual = [
			'type' => ReblogInfoChanged::class,
			'contentId' => '536ef534-6820-4dba-b857-9e81792bdd5e',
			'userId' => '32eba133-c034-4575-a70d-37e2364c3bc6',
			'siteId' => 'd177f415-52bf-4726-a013-3e03767aa619',
			'id' => 'c8247b5a-5dd5-4d43-af48-61000bbc0bc1',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'url' => '//smol.blog/',
				'info' => ['title' => 'No strangers', 'embed' => '<iframe></iframe>'],
			],
		];

		$this->assertEquals($expected, ContentEvent::fromTypedArray($actual));
	}
	public function testItWillSerializeCorrectly() {
		$actual = new ReblogInfoChanged(
			url: '//smol.blog/',
			info: new ExternalContentInfo(title: 'No strangers', embed: '<iframe></iframe>'),
			contentId: Identifier::fromString('536ef534-6820-4dba-b857-9e81792bdd5e'),
			userId: Identifier::fromString('32eba133-c034-4575-a70d-37e2364c3bc6'),
			siteId: Identifier::fromString('d177f415-52bf-4726-a013-3e03767aa619'),
			id: Identifier::fromString('c8247b5a-5dd5-4d43-af48-61000bbc0bc1'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);

		$expected = [
			'type' => ReblogInfoChanged::class,
			'contentId' => '536ef534-6820-4dba-b857-9e81792bdd5e',
			'userId' => '32eba133-c034-4575-a70d-37e2364c3bc6',
			'siteId' => 'd177f415-52bf-4726-a013-3e03767aa619',
			'id' => 'c8247b5a-5dd5-4d43-af48-61000bbc0bc1',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'url' => '//smol.blog/',
				'info' => ['title' => 'No strangers', 'embed' => '<iframe></iframe>'],
			],
		];

		$this->assertEquals($expected, $actual->toArray());
	}
}
