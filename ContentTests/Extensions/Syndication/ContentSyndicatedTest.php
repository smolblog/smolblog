<?php

namespace Smolblog\Core\ContentV1\Extensions\Syndication;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Core\ContentV1\Events\ContentEvent;
use Smolblog\Foundation\Value\Fields\Identifier;

class ContentSyndicatedTest extends TestCase {
	public function testLinksCanBeSetAndRetrieved() {
		$links = new Syndication(
			links: [
				new SyndicationLink(url: '//one.com/'),
				new SyndicationLink(url: '//two.com/'),
				new SyndicationLink(url: '//smol.blog/'),
			],
			channels: []
		);

		$event = new ContentSyndicated(
			url: '//smol.blog/',
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);
		$event->setSyndicationState($links);

		$this->assertEquals($event->contentId, $event->getContentId());
		$this->assertEquals($links, $event->getNewExtension());
	}

	public function testItWillSerializeCorrectly() {
		$expected = [
			'type' => ContentSyndicated::class,
			'contentId' => '7fe339e8-459b-4a48-8e30-e6638dc5ceb5',
			'userId' => 'f8e10d2e-9f72-447a-8376-0007b14d94e7',
			'siteId' => 'bd991aac-bd81-4ee7-b77c-793d4bc55796',
			'id' => '20366a42-2839-41c7-83a9-3a00cb411c7d',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'url' => '//smol.blog/post/123',
				'channelId' => 'a30b760a-35fb-40d3-bf53-da60d6406475',
			],
		];

		$actual = new ContentSyndicated(
			contentId: Identifier::fromString('7fe339e8-459b-4a48-8e30-e6638dc5ceb5'),
			userId: Identifier::fromString('f8e10d2e-9f72-447a-8376-0007b14d94e7'),
			siteId: Identifier::fromString('bd991aac-bd81-4ee7-b77c-793d4bc55796'),
			id: Identifier::fromString('20366a42-2839-41c7-83a9-3a00cb411c7d'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
			url: '//smol.blog/post/123',
			channelId: Identifier::fromString('a30b760a-35fb-40d3-bf53-da60d6406475'),
		);

		$this->assertEquals($expected, $actual->serializeValue());
	}

	public function testItWillDeserializeCorrectly() {
		$actual = [
			'type' => ContentSyndicated::class,
			'contentId' => '7fe339e8-459b-4a48-8e30-e6638dc5ceb5',
			'userId' => 'f8e10d2e-9f72-447a-8376-0007b14d94e7',
			'siteId' => 'bd991aac-bd81-4ee7-b77c-793d4bc55796',
			'id' => '20366a42-2839-41c7-83a9-3a00cb411c7d',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'url' => '//smol.blog/post/123',
				'channelId' => 'a30b760a-35fb-40d3-bf53-da60d6406475',
			],
		];

		$expected = new ContentSyndicated(
			contentId: Identifier::fromString('7fe339e8-459b-4a48-8e30-e6638dc5ceb5'),
			userId: Identifier::fromString('f8e10d2e-9f72-447a-8376-0007b14d94e7'),
			siteId: Identifier::fromString('bd991aac-bd81-4ee7-b77c-793d4bc55796'),
			id: Identifier::fromString('20366a42-2839-41c7-83a9-3a00cb411c7d'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
			url: '//smol.blog/post/123',
			channelId: Identifier::fromString('a30b760a-35fb-40d3-bf53-da60d6406475'),
		);

		$this->assertEquals($expected, ContentEvent::fromTypedArray($actual));
	}
}
