<?php

namespace Smolblog\Foundation\Value\Messages;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;
use Smolblog\Test\TestCase;

readonly class ExampleDomainEvent extends DomainEvent {
	public function __construct(
		Identifier $id,
		DateTimeField $timestamp,
		Identifier $userId,
		?Identifier $aggregateId,
		?Identifier $entityId,
		public string $content
	) {
		parent::__construct(
			id: $id,
			timestamp: $timestamp,
			userId: $userId,
			aggregateId: $aggregateId,
			entityId: $entityId
		);
	}
}

#[CoversClass(DomainEvent::class)]
final class DomainEventTest extends TestCase {
	#[TestDox('It will add a type field to the serialization and deserialize without it.')]
	public function testSerialization() {
		$object = new ExampleDomainEvent(
			id: Identifier::fromString('87642fa1-9fd0-41e0-9f08-022a231576e6'),
			timestamp: new DateTimeField('2022-02-22 22:22:22'),
			userId: Identifier::fromString('659b4726-6d67-4e7d-b5ac-09df89c6ed25'),
			aggregateId: Identifier::fromString('8e27d1dd-4dc8-437a-bcf5-44fd94fbdcd2'),
			entityId: Identifier::fromString('676941e7-09bb-4aa9-bf05-ff20f03f9fe4'),
			content: 'hello'
		);
		$array = [
			'id' => '87642fa1-9fd0-41e0-9f08-022a231576e6',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'userId' => '659b4726-6d67-4e7d-b5ac-09df89c6ed25',
			'aggregateId' => '8e27d1dd-4dc8-437a-bcf5-44fd94fbdcd2',
			'entityId' => '676941e7-09bb-4aa9-bf05-ff20f03f9fe4',
			'type' => ExampleDomainEvent::class,
			'content' => 'hello'
		];

		$this->assertEquals($object, ExampleDomainEvent::deserializeValue($array));
		$this->assertEquals($array, $object->serializeValue());
	}

	public static function knownEvents(): array {
		return [
			'the test subclass' => [
				new ExampleDomainEvent(
					id: Identifier::fromString('87642fa1-9fd0-41e0-9f08-022a231576e6'),
					timestamp: new DateTimeField('2022-02-22 22:22:22'),
					userId: Identifier::fromString('659b4726-6d67-4e7d-b5ac-09df89c6ed25'),
					aggregateId: Identifier::fromString('8e27d1dd-4dc8-437a-bcf5-44fd94fbdcd2'),
					entityId: Identifier::fromString('676941e7-09bb-4aa9-bf05-ff20f03f9fe4'),
					content: 'hello'
				)
			],
			'an anonymous subclass' => [
				new readonly class(
					content: 'hello',
					id: new RandomIdentifier(),
					timestamp: new DateTimeField(),
					userId: new RandomIdentifier(),
				) extends DomainEvent {
					public function __construct(
						public string $content,
						Identifier $id,
						DateTimeField $timestamp,
						Identifier $userId,
						?Identifier $aggregateId = null,
						?Identifier $entityId	= null,
					) {
						parent::__construct(
							id: $id,
							timestamp: $timestamp,
							userId: $userId,
							aggregateId: $aggregateId,
							entityId: $entityId
						);
					}
				}
			],
		];
	}

	#[DataProvider('knownEvents')]
	#[TestDox('It will deserialize $_dataName to its original class.')]
	public function testKnownEvents(DomainEvent $object) {
		$deserialized = DomainEvent::deserializeValue($object->serializeValue());

		$this->assertInstanceOf(get_class($object), $deserialized);
		$this->assertEquals($object->serializeValue(), $deserialized->serializeValue());
	}

	public static function unknownEvents(): array {
		$anon = new readonly class(
			content: 'hello',
			id: Identifier::fromString('38290186-3fbb-4396-8130-84f1061069e1'),
			timestamp: new DateTimeField('2022-02-22 22:22:22'),
			userId: Identifier::fromString('ffdad9e5-67e8-47c1-964c-c18df0b2264a'),
		) extends Value implements SerializableValue{
			use SerializableValueKit;
			public function __construct(
				public string $content,
				public Identifier $id,
				public DateTimeField $timestamp,
				public Identifier $userId,
			) {
			}
		};

		return [
			'a class that does not exist' => [
				'serialized' => [
					'id' => '41c4b95a-4186-4c98-8a39-9054c413e58a',
					'timestamp' => '2022-02-22T22:22:22.000+00:00',
					'userId' => '8663bcea-4998-4e98-a74a-9e8e6c917893',
					'aggregateId' => '4cbd3412-9341-40b2-960b-e78933db1ba7',
					'entityId' => 'ce9a614a-c1a2-4f7b-b0dc-508ade5f2771',
					'type' => 'ClassThatDoesNotExist',
					'content' => 'hello',
				],
				'expected' => new UnknownDomainEvent(
					id: Identifier::fromString('41c4b95a-4186-4c98-8a39-9054c413e58a'),
					timestamp: new DateTimeField('2022-02-22 22:22:22'),
					userId: Identifier::fromString('8663bcea-4998-4e98-a74a-9e8e6c917893'),
					aggregateId: Identifier::fromString('4cbd3412-9341-40b2-960b-e78933db1ba7'),
					entityId: Identifier::fromString('ce9a614a-c1a2-4f7b-b0dc-508ade5f2771'),
					props: ['type' => 'ClassThatDoesNotExist', 'content' => 'hello']
				),
			],
			'a non-DomainEvent subclass' => [
				'serialized' => [
					...$anon->serializeValue(),
					'type' => get_class($anon),
				],
				'expected' => new UnknownDomainEvent(
					id: Identifier::fromString('38290186-3fbb-4396-8130-84f1061069e1'),
					timestamp: new DateTimeField('2022-02-22 22:22:22'),
					userId: Identifier::fromString('ffdad9e5-67e8-47c1-964c-c18df0b2264a'),
					props: ['type' => get_class($anon), 'content' => 'hello']
				),
			],
			'an object serialized without a type' => [
				'serialized' => [
					'id' => '38290186-3fbb-4396-8130-84f1061069e1',
					'timestamp' => '2022-02-22T22:22:22.000+00:00',
					'userId' => 'ffdad9e5-67e8-47c1-964c-c18df0b2264a',
					'content' => 'hello',
				],
				'expected' => new UnknownDomainEvent(
					id: Identifier::fromString('38290186-3fbb-4396-8130-84f1061069e1'),
					timestamp: new DateTimeField('2022-02-22 22:22:22'),
					userId: Identifier::fromString('ffdad9e5-67e8-47c1-964c-c18df0b2264a'),
					props: ['content' => 'hello']
				),
			],
		];
	}

	#[DataProvider('unknownEvents')]
	#[TestDox('It will deserialize $_dataName to an UnknownDomainEvent.')]
	public function testUnknownEvents(array $serialized, UnknownDomainEvent $expected) {
		$deserialized = DomainEvent::deserializeValue($serialized);

		$this->assertInstanceOf(UnknownDomainEvent::class, $deserialized);
		$this->assertEquals($expected, $deserialized);
	}
}
