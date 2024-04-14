<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Core\ContentV1\Events\ContentEvent;
use Smolblog\Foundation\Value\Fields\Identifier;

class ReblogCreatedTest extends TestCase {
	public function testItWillSerializeCorrectly() {
		$expected = [
			'type' => ReblogCreated::class,
			'contentId' => '0c24e971-cfdc-4722-9327-dd375d49941a',
			'userId' => '0283e173-7ded-4203-bc0a-31bd5185b155',
			'siteId' => '0d90c7f1-e1c8-46da-9eb7-b065d73a8f56',
			'id' => '61859efd-7c77-4daa-99b4-7b57d70b8606',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'authorId' => 'e2390683-ed21-44e3-b7e2-6c77760bdef9',
				'url' => '//smol.blog/',
				'comment' => 'Another thing.',
				'info' => ['title' => 'Hello', 'embed' => '<iframe></iframe>'],
			]
		];

		$actual = new ReblogCreated(
			url: '//smol.blog/',
			authorId: Identifier::fromString('e2390683-ed21-44e3-b7e2-6c77760bdef9'),
			contentId: Identifier::fromString('0c24e971-cfdc-4722-9327-dd375d49941a'),
			userId: Identifier::fromString('0283e173-7ded-4203-bc0a-31bd5185b155'),
			siteId: Identifier::fromString('0d90c7f1-e1c8-46da-9eb7-b065d73a8f56'),
			comment: 'Another thing.',
			info: new ExternalContentInfo(title: 'Hello', embed: '<iframe></iframe>'),
			id: Identifier::fromString('61859efd-7c77-4daa-99b4-7b57d70b8606'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);

		$this->assertEquals($expected, $actual->serializeValue());
	}
	public function testItWillDeserializeCorrectly() {
		$actual = [
			'type' => ReblogCreated::class,
			'contentId' => '0c24e971-cfdc-4722-9327-dd375d49941a',
			'userId' => '0283e173-7ded-4203-bc0a-31bd5185b155',
			'siteId' => '0d90c7f1-e1c8-46da-9eb7-b065d73a8f56',
			'id' => '61859efd-7c77-4daa-99b4-7b57d70b8606',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'authorId' => 'e2390683-ed21-44e3-b7e2-6c77760bdef9',
				'url' => '//smol.blog/',
				'comment' => 'Another thing.',
				'info' => ['title' => 'Hello', 'embed' => '<iframe></iframe>'],
			]
		];

		$expected = new ReblogCreated(
			url: '//smol.blog/',
			authorId: Identifier::fromString('e2390683-ed21-44e3-b7e2-6c77760bdef9'),
			contentId: Identifier::fromString('0c24e971-cfdc-4722-9327-dd375d49941a'),
			userId: Identifier::fromString('0283e173-7ded-4203-bc0a-31bd5185b155'),
			siteId: Identifier::fromString('0d90c7f1-e1c8-46da-9eb7-b065d73a8f56'),
			comment: 'Another thing.',
			info: new ExternalContentInfo(title: 'Hello', embed: '<iframe></iframe>'),
			id: Identifier::fromString('61859efd-7c77-4daa-99b4-7b57d70b8606'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);

		$this->assertEquals($expected, ContentEvent::fromTypedArray($actual));
	}

	public function testItWillCreateTheTitleAndBody() {
		$event = new ReblogCreated(
			url: '//smol.blog/',
			authorId: Identifier::fromString('e2390683-ed21-44e3-b7e2-6c77760bdef9'),
			contentId: Identifier::fromString('0c24e971-cfdc-4722-9327-dd375d49941a'),
			userId: Identifier::fromString('0283e173-7ded-4203-bc0a-31bd5185b155'),
			siteId: Identifier::fromString('0d90c7f1-e1c8-46da-9eb7-b065d73a8f56'),
			comment: 'Another thing.',
			info: new ExternalContentInfo(title: 'Hello', embed: '<iframe></iframe>'),
			id: Identifier::fromString('61859efd-7c77-4daa-99b4-7b57d70b8606'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);
		$event->setMarkdownHtml(['<p>Another thing.</p>']);

		$this->assertEquals('Hello', $event->getNewTitle());
		$this->assertEquals('<p>Another thing.</p>', $event->getCommentHtml());
		$this->assertEquals("<iframe></iframe>\n\n<p>Another thing.</p>", $event->getNewBody());
	}

	public function testItCreatesAReblog() {
		$event = new ReblogCreated(
			url: '//smol.blog/',
			authorId: Identifier::fromString('e2390683-ed21-44e3-b7e2-6c77760bdef9'),
			contentId: Identifier::fromString('0c24e971-cfdc-4722-9327-dd375d49941a'),
			userId: Identifier::fromString('0283e173-7ded-4203-bc0a-31bd5185b155'),
			siteId: Identifier::fromString('0d90c7f1-e1c8-46da-9eb7-b065d73a8f56'),
			comment: 'Another thing.',
			info: new ExternalContentInfo(title: 'Hello', embed: '<iframe></iframe>'),
			id: Identifier::fromString('61859efd-7c77-4daa-99b4-7b57d70b8606'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);

		$this->assertEquals('reblog', $event->getContentType());
	}
}
