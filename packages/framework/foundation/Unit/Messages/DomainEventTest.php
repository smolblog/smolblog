<?php

namespace Smolblog\Framework\Foundation\Messages;
use Smolblog\Framework\Foundation\Value;
use Smolblog\Framework\Foundation\Values\DateTime;
use Smolblog\Framework\Foundation\Values\Identifier;
use Smolblog\Framework\Foundation\Values\RandomIdentifier;

readonly class DomainEventTest extends DomainEvent {
	public function __construct(
		Identifier $id,
		DateTime $timestamp,
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

it('will add a type field to the serialization and deserialize without it', function() {
	$object = new DomainEventTest(
		id: Identifier::fromString('87642fa1-9fd0-41e0-9f08-022a231576e6'),
		timestamp: new DateTime('2022-02-22 22:22:22'),
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
		'type' => DomainEventTest::class,
		'content' => 'hello'
	];

	expect($object->toArray())->toEqual($array);
	expect(DomainEventTest::fromArray($array))->toEqual($object);
});

test('the parent class can deserialize any domain event', function(DomainEvent $object) {
	$deserialized = DomainEvent::deserializeWithType($object->toArray());

	expect($deserialized)->toBeInstanceOf(get_class($object));
	expect($deserialized->toArray())->toEqual($object->toArray());
})->with([
	'the test class' => new DomainEventTest(
		id: Identifier::fromString('87642fa1-9fd0-41e0-9f08-022a231576e6'),
		timestamp: new DateTime('2022-02-22 22:22:22'),
		userId: Identifier::fromString('659b4726-6d67-4e7d-b5ac-09df89c6ed25'),
		aggregateId: Identifier::fromString('8e27d1dd-4dc8-437a-bcf5-44fd94fbdcd2'),
		entityId: Identifier::fromString('676941e7-09bb-4aa9-bf05-ff20f03f9fe4'),
		content: 'hello'
	),
	'an anonymous class' => new readonly class(
		content: 'hello',
		id: new RandomIdentifier(),
		timestamp: new DateTime(),
		userId: new RandomIdentifier(),
	) extends DomainEvent {
		public function __construct(
			public string $content,
			Identifier $id,
			DateTime $timestamp,
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
	},
]);

test(
	'the parent class will deserialize an unknown class to an UnknownDomainEvent',
	function(array $serialized, UnknownDomainEvent $expected) {
		$original = [
			'id' => '41c4b95a-4186-4c98-8a39-9054c413e58a',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'userId' => '8663bcea-4998-4e98-a74a-9e8e6c917893',
			'aggregateId' => '4cbd3412-9341-40b2-960b-e78933db1ba7',
			'entityId' => 'ce9a614a-c1a2-4f7b-b0dc-508ade5f2771',
			'type' => 'ClassThatDoesNotExist',
			'content' => 'hello',
		];
		$expected = new UnknownDomainEvent(
			id: Identifier::fromString('41c4b95a-4186-4c98-8a39-9054c413e58a'),
			timestamp: new DateTime('2022-02-22 22:22:22'),
			userId: Identifier::fromString('8663bcea-4998-4e98-a74a-9e8e6c917893'),
			aggregateId: Identifier::fromString('4cbd3412-9341-40b2-960b-e78933db1ba7'),
			entityId: Identifier::fromString('ce9a614a-c1a2-4f7b-b0dc-508ade5f2771'),
			props: ['type' => 'ClassThatDoesNotExist', 'content' => 'hello']
		);

		$deserialized = DomainEvent::deserializeWithType($original);

		expect($deserialized)->toBeInstanceOf(UnknownDomainEvent::class);
		expect($deserialized)->toEqual($expected);
	}
)->with([
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
			timestamp: new DateTime('2022-02-22 22:22:22'),
			userId: Identifier::fromString('8663bcea-4998-4e98-a74a-9e8e6c917893'),
			aggregateId: Identifier::fromString('4cbd3412-9341-40b2-960b-e78933db1ba7'),
			entityId: Identifier::fromString('ce9a614a-c1a2-4f7b-b0dc-508ade5f2771'),
			props: ['type' => 'ClassThatDoesNotExist', 'content' => 'hello']
		),
	],
	'a non-DomainEvent subclass' => function() {
		$original = new readonly class(
			content: 'hello',
			id: Identifier::fromString('38290186-3fbb-4396-8130-84f1061069e1'),
			timestamp: new DateTime('2022-02-22 22:22:22'),
			userId: Identifier::fromString('ffdad9e5-67e8-47c1-964c-c18df0b2264a'),
		) extends Value {
			public function __construct(
				public string $content,
				public Identifier $id,
				public DateTime $timestamp,
				public Identifier $userId,
			) {
			}
		};
		return [
			'serialized' => [
				...$original->toArray(),
				'type' => get_class($original),
			],
			'expected' => new UnknownDomainEvent(
				id: Identifier::fromString('38290186-3fbb-4396-8130-84f1061069e1'),
				timestamp: new DateTime('2022-02-22 22:22:22'),
				userId: Identifier::fromString('ffdad9e5-67e8-47c1-964c-c18df0b2264a'),
				props: ['type' => get_class($original), 'content' => 'hello']
			),
		];
	},
	'an object serialized without a type' => [
		'serialized' => [
			'id' => '38290186-3fbb-4396-8130-84f1061069e1',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'userId' => 'ffdad9e5-67e8-47c1-964c-c18df0b2264a',
			'content' => 'hello',
		],
		'expected' => new UnknownDomainEvent(
			id: Identifier::fromString('38290186-3fbb-4396-8130-84f1061069e1'),
			timestamp: new DateTime('2022-02-22 22:22:22'),
			userId: Identifier::fromString('ffdad9e5-67e8-47c1-964c-c18df0b2264a'),
			props: ['content' => 'hello']
		),
	],
]);
