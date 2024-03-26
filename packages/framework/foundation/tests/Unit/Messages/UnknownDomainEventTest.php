<?php
use Smolblog\Framework\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Framework\Foundation\Messages\UnknownDomainEvent;
use Smolblog\Framework\Foundation\Values\DateTime;
use Smolblog\Framework\Foundation\Values\Identifier;

it('will deserialize an array with props like any value', function() {
	$array = [
		'id' => 'fb0914b3-0224-4150-bd4b-2934aaddf9be',
		'timestamp' => '2022-02-22T22:22:22.000+00:00',
		'userId' => '659b4726-6d67-4e7d-b5ac-09df89c6ed25',
		'aggregateId' => '8e27d1dd-4dc8-437a-bcf5-44fd94fbdcd2',
		'entityId' => '676941e7-09bb-4aa9-bf05-ff20f03f9fe4',
		'type' => 'ClassThatDoesNotExist',
		'content' => 'hello',
		'props' => [
			'one' => 'two',
			'three' => 'four',
		],
	];

	$expected = new UnknownDomainEvent(
		id: Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'),
		timestamp: new DateTime('2022-02-22 22:22:22'),
		userId: Identifier::fromString('659b4726-6d67-4e7d-b5ac-09df89c6ed25'),
		aggregateId: Identifier::fromString('8e27d1dd-4dc8-437a-bcf5-44fd94fbdcd2'),
		entityId: Identifier::fromString('676941e7-09bb-4aa9-bf05-ff20f03f9fe4'),
		props: ['one' => 'two', 'three' => 'four'],
	);
	$actual = UnknownDomainEvent::fromArray($array);

	expect($actual)->toBeInstanceOf(UnknownDomainEvent::class);
	expect($actual)->toEqual($expected);
});

it('will deserialize any non-DomainEvent values to props', function() {
	$array = [
		'id' => 'fb0914b3-0224-4150-bd4b-2934aaddf9be',
		'timestamp' => '2022-02-22T22:22:22.000+00:00',
		'userId' => '659b4726-6d67-4e7d-b5ac-09df89c6ed25',
		'type' => 'ClassThatDoesNotExist',
		'content' => 'hello',
		'one' => 'two',
		'three' => 'four',
		'canIHave' => 'aLittleMore',
	];

	$expected = new UnknownDomainEvent(
		id: Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'),
		timestamp: new DateTime('2022-02-22 22:22:22'),
		userId: Identifier::fromString('659b4726-6d67-4e7d-b5ac-09df89c6ed25'),
		props: [
			'type' => 'ClassThatDoesNotExist',
			'content' => 'hello',
			'one' => 'two',
			'three' => 'four',
			'canIHave' => 'aLittleMore'
		],
	);
	$actual = UnknownDomainEvent::fromArray($array);

	expect($actual)->toBeInstanceOf(UnknownDomainEvent::class);
	expect($actual)->toEqual($expected);
});

it('will catch errors and throw an exception', function(array $array) {
	expect(fn() => UnknownDomainEvent::fromArray($array))->toThrow(InvalidValueProperties::class);
})->with([
	'missing DomainEvent properties' => [[
		'timestamp' => '2022-02-22T22:22:22.000+00:00',
		'userId' => '659b4726-6d67-4e7d-b5ac-09df89c6ed25',
		'type' => 'ClassThatDoesNotExist',
		'content' => 'hello',
	]],
	'invalid Identifiers' => [[
		'id' => 'fb0914b3-0224-4150-bd4b-2934aaddf9beQQQ',
		'timestamp' => '2022-02-22T22:22:22.000+00:00',
		'userId' => '659b4726-6d67-4e7d-b5ac-QQQ09df89c6ed25',
	]],
	'an invalid DateTime' => [[
		'id' => 'fb0914b3-0224-4150-bd4b-2934aaddf9be',
		'timestamp' => '2022-02-22T22:22:22.000+00:00QQQ',
		'userId' => '659b4726-6d67-4e7d-b5ac-09df89c6ed25',
	]],
]);
