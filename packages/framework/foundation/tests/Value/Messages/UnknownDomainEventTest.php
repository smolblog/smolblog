<?php

namespace Smolblog\Foundation\Value\Messages;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\TestCase;

#[CoversClass(UnknownDomainEvent::class)]
final class UnknownDomainEventTest extends TestCase {
	#[TestDox('It will deserialize an array with props like any value')]
	public function testArrayWithProps() {
		$expected = new UnknownDomainEvent(
			id: Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'),
			timestamp: new DateTimeField('2022-02-22 22:22:22'),
			userId: Identifier::fromString('659b4726-6d67-4e7d-b5ac-09df89c6ed25'),
			aggregateId: Identifier::fromString('8e27d1dd-4dc8-437a-bcf5-44fd94fbdcd2'),
			entityId: Identifier::fromString('676941e7-09bb-4aa9-bf05-ff20f03f9fe4'),
			props: ['one' => 'two', 'three' => 'four'],
		);
		$actual = UnknownDomainEvent::deserializeValue([
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
		]);

		$this->assertEquals($expected, $actual);
	}

	#[TestDox('It will deserialize any non-DomainEvent values to props')]
	public function testOtherValues() {
		$expected = new UnknownDomainEvent(
			id: Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'),
			timestamp: new DateTimeField('2022-02-22 22:22:22'),
			userId: Identifier::fromString('659b4726-6d67-4e7d-b5ac-09df89c6ed25'),
			props: [
				'type' => 'ClassThatDoesNotExist',
				'content' => 'hello',
				'one' => 'two',
				'three' => 'four',
				'canIHave' => 'aLittleMore'
			],
		);
		$actual = UnknownDomainEvent::deserializeValue([
			'id' => 'fb0914b3-0224-4150-bd4b-2934aaddf9be',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'userId' => '659b4726-6d67-4e7d-b5ac-09df89c6ed25',
			'type' => 'ClassThatDoesNotExist',
			'content' => 'hello',
			'one' => 'two',
			'three' => 'four',
			'canIHave' => 'aLittleMore',
		]);

		$this->assertEquals($expected, $actual);
	}

	public static function badArrays(): array {
		return [
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
		];
	}

	#[DataProvider('badArrays')]
	#[TestDox('It will catch $_dataName and throw an exception')]
	public function testExceptions(array $array) {
		$this->expectException(InvalidValueProperties::class);

		UnknownDomainEvent::deserializeValue($array);
	}
}
