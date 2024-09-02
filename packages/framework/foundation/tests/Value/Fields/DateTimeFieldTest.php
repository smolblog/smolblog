<?php

namespace Smolblog\Foundation\Value\Fields;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\TestCase;

#[CoversClass(DateTimeField::class)]
final class DateTimeFieldTest extends TestCase {
	public static function constructorTypes() {
		return [
			'a string' => [
				new DateTimeField(datetime:'2022-02-22 22:22:22'),
				'2022-02-22T22:22:22.000+00:00'
			],
			'a string and a timezone' => [
				new DateTimeField(datetime:'2022-02-22 22:22:22', timezone: new DateTimeZone('America/New_York')),
				'2022-02-22T22:22:22.000-05:00'
			]
		];
	}

	#[DataProvider('constructorTypes')]
	#[TestDox('can be created with $_dataName')]
	public function testConstructor(DateTimeField $date, string $expected) {
		$this->assertInstanceOf(DateTimeField::class, $date);
		$this->assertEquals($expected, $date->object->format(DateTimeInterface::RFC3339_EXTENDED));
	}

	#[TestDox('will serialize to and deserialize from a string')]
	public function testSerialization() {
		$dateString = '2022-02-22T22:22:22.000+00:00';
		$dateObject = new DateTimeField(datetime: $dateString);

		$this->assertEquals($dateString, $dateObject->toString());
		$this->assertEquals($dateObject, DateTimeField::fromString($dateString));
	}

	#[TestDox('will throw an exception when the given string is invalid')]
	public function testConstructionException() {
		$this->expectException(InvalidValueProperties::class);

		new DateTimeField(datetime: '2022-02-22T25:22:22.000+00:00');
	}

	#[TestDox('will throw an exception when the serialized string is invalid')]
	public function testSerializationException() {
		$this->expectException(InvalidValueProperties::class);

		DateTimeField::fromString('2022-02-22T25:22:22.000+00:00');
	}
}
