<?php

namespace Smolblog\Core\Content\Events;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Exceptions\InvalidMessageAttributesException;
use Smolblog\Framework\Objects\Identifier;

final class ContentBaseAttributeEditedTest extends TestCase {
	public function testItCanBeInstantiatedWithValidFields() {
		$this->assertInstanceOf(
			ContentBaseAttributeEdited::class,
			new ContentBaseAttributeEdited(
				contentId: $this->randomId(),
				userId: $this->randomId(),
				siteId: $this->randomId(),
				authorId: $this->randomId(),
				publishTimestamp: new DateTimeImmutable(),
			)
		);

		$this->assertInstanceOf(
			ContentBaseAttributeEdited::class,
			new ContentBaseAttributeEdited(
				contentId: $this->randomId(),
				userId: $this->randomId(),
				siteId: $this->randomId(),
				authorId: $this->randomId(),
			)
		);

		$this->assertInstanceOf(
			ContentBaseAttributeEdited::class,
			new ContentBaseAttributeEdited(
				contentId: $this->randomId(),
				userId: $this->randomId(),
				siteId: $this->randomId(),
				publishTimestamp: new DateTimeImmutable(),
			)
		);
	}

	public function testItThrowsAnErrorIfItIsMissingAllFields() {
		$this->expectException(InvalidMessageAttributesException::class);

		new ContentBaseAttributeEdited(
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);
	}

	public function testItWillDeserializeFromArray() {
		$expected = new ContentBaseAttributeEdited(
			contentId: Identifier::fromString('7fe339e8-459b-4a48-8e30-e6638dc5ceb5'),
			userId: Identifier::fromString('f8e10d2e-9f72-447a-8376-0007b14d94e7'),
			siteId: Identifier::fromString('bd991aac-bd81-4ee7-b77c-793d4bc55796'),
			authorId: Identifier::fromString('376ee1ba-4544-4e9e-827f-2792b0c67c76'),
			publishTimestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
			id: Identifier::fromString('20366a42-2839-41c7-83a9-3a00cb411c7d'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);

		$actual = ContentEvent::fromTypedArray([
			'type' => ContentBaseAttributeEdited::class,
			'contentId' => '7fe339e8-459b-4a48-8e30-e6638dc5ceb5',
			'userId' => 'f8e10d2e-9f72-447a-8376-0007b14d94e7',
			'siteId' => 'bd991aac-bd81-4ee7-b77c-793d4bc55796',
			'id' => '20366a42-2839-41c7-83a9-3a00cb411c7d',
			'timestamp' => '2022-02-22T22:22:22',
			'payload' => [
				'authorId' => '376ee1ba-4544-4e9e-827f-2792b0c67c76',
				'publishTimestamp' => '2022-02-22T22:22:22',
			]
		]);

		$this->assertEquals($expected, $actual);
	}

	public function testItWillSerializeToArray() {
		$expected = [
			'type' => ContentBaseAttributeEdited::class,
			'contentId' => '7fe339e8-459b-4a48-8e30-e6638dc5ceb5',
			'userId' => 'f8e10d2e-9f72-447a-8376-0007b14d94e7',
			'siteId' => 'bd991aac-bd81-4ee7-b77c-793d4bc55796',
			'id' => '20366a42-2839-41c7-83a9-3a00cb411c7d',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [
				'authorId' => '376ee1ba-4544-4e9e-827f-2792b0c67c76',
				'publishTimestamp' => '2022-02-22T22:22:22.000+00:00',
			]
		];

		$actual = (new ContentBaseAttributeEdited(
			contentId: Identifier::fromString('7fe339e8-459b-4a48-8e30-e6638dc5ceb5'),
			userId: Identifier::fromString('f8e10d2e-9f72-447a-8376-0007b14d94e7'),
			siteId: Identifier::fromString('bd991aac-bd81-4ee7-b77c-793d4bc55796'),
			authorId: Identifier::fromString('376ee1ba-4544-4e9e-827f-2792b0c67c76'),
			publishTimestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
			id: Identifier::fromString('20366a42-2839-41c7-83a9-3a00cb411c7d'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		))->toArray();

		$this->assertEquals($expected, $actual);
	}
}
