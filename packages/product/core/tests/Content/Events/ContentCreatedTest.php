<?php

namespace Smolblog\Core\Content\Events;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\GenericContent;
use Smolblog\Core\Content\InvalidContentException;
use Smolblog\Foundation\Value\Fields\Identifier;

final class TestContentCreated extends ContentCreated {
	public function __construct(public readonly string $one = 'two', ...$props) {
		parent::__construct(...$props);
	}
	public function getNewBody(): string { return '<p>hullo</p>'; }
	public function getNewTitle(): string { return 'Hullo'; }
	public function getContentPayload(): array { return ['one' => 'two']; }
	public function getContentType(): string { return GenericContent::class; }
}

final class ContentCreatedTest extends TestCase {
	public function testUnprovidedInformationRemainsNull() {
		$actual = new TestContentCreated(
			authorId: $this->randomId(),
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$this->assertInstanceOf(ContentCreated::class, $actual);
		$this->assertNull($actual->publishTimestamp);
	}

	public function testItSerializesAPayloadCorrectly() {
		$expected = [
			'type' => TestContentCreated::class,
			'contentId' => '7fe339e8-459b-4a48-8e30-e6638dc5ceb5',
			'userId' => 'f8e10d2e-9f72-447a-8376-0007b14d94e7',
			'siteId' => 'bd991aac-bd81-4ee7-b77c-793d4bc55796',
			'id' => '20366a42-2839-41c7-83a9-3a00cb411c7d',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'authorId' => '376ee1ba-4544-4e9e-827f-2792b0c67c76',
				'one' => 'two',
			]
		];

		$actual = new TestContentCreated(
			contentId: Identifier::fromString('7fe339e8-459b-4a48-8e30-e6638dc5ceb5'),
			userId: Identifier::fromString('f8e10d2e-9f72-447a-8376-0007b14d94e7'),
			siteId: Identifier::fromString('bd991aac-bd81-4ee7-b77c-793d4bc55796'),
			authorId: Identifier::fromString('376ee1ba-4544-4e9e-827f-2792b0c67c76'),
			id: Identifier::fromString('20366a42-2839-41c7-83a9-3a00cb411c7d'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);

		$this->assertEquals($expected, $actual->toArray());
	}

	public function testItDeserializesAPayloadCorrectly() {
		$expected = new TestContentCreated(
			contentId: Identifier::fromString('7fe339e8-459b-4a48-8e30-e6638dc5ceb5'),
			userId: Identifier::fromString('f8e10d2e-9f72-447a-8376-0007b14d94e7'),
			siteId: Identifier::fromString('bd991aac-bd81-4ee7-b77c-793d4bc55796'),
			authorId: Identifier::fromString('376ee1ba-4544-4e9e-827f-2792b0c67c76'),
			id: Identifier::fromString('20366a42-2839-41c7-83a9-3a00cb411c7d'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
			one: 'hello',
		);

		$actual = [
			'type' => TestContentCreated::class,
			'contentId' => '7fe339e8-459b-4a48-8e30-e6638dc5ceb5',
			'userId' => 'f8e10d2e-9f72-447a-8376-0007b14d94e7',
			'siteId' => 'bd991aac-bd81-4ee7-b77c-793d4bc55796',
			'id' => '20366a42-2839-41c7-83a9-3a00cb411c7d',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'authorId' => '376ee1ba-4544-4e9e-827f-2792b0c67c76',
				'one' => 'hello',
			]
		];

		$this->assertEquals($expected, ContentEvent::fromTypedArray($actual));
	}
}
