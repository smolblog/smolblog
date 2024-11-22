<?php

namespace Smolblog\Foundation\Value\Fields;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\TestCase;

#[CoversClass(Identifier::class)]
#[CoversClass(RandomIdentifier::class)]
#[CoversClass(DateIdentifier::class)]
#[CoversClass(NamedIdentifier::class)]
final class IdentifierTest extends TestCase {
	#[TestDox('RandomIdentifier is a random identifier')]
	public function testRandom() {
		$this->assertInstanceOf(Identifier::class, new RandomIdentifier());
	}

	#[TestDox('DateIdentifier can be created with a specfic date')]
	public function testDateString() {
		$date = new DateTimeImmutable('2022-02-22 22:22:22');
		$this->assertInstanceOf(Identifier::class, new DateIdentifier($date));
	}

	#[TestDox('DateIdentifier can be created with a DateTimeField object')]
	public function testDateObject() {
		$date = new DateTimeField('2022-02-22 22:22:22');
		$this->assertInstanceOf(Identifier::class, new DateIdentifier($date));
	}

	#[TestDox('DateIdentifier will be created with the current date by default')]
	public function testDateDefault() {
		$this->assertInstanceOf(Identifier::class, new DateIdentifier());
	}

	#[TestDox('NamedIdentifier can be created with a namespace and name')]
	public function testNamedGood() {
		$this->assertInstanceOf(
			Identifier::class,
			new NamedIdentifier(NamedIdentifier::NAMESPACE_URL, 'https://smol.blog/post/123')
		);
	}

	#[TestDox('The namespace in a NamedIdentifier must be a UUID string')]
	public function testNamedBad() {
		$this->expectException(InvalidArgumentException::class);

		new NamedIdentifier('not-a-uuid', 'https://smol.blog/post/123');
	}

	#[TestDox('NamedIdentifier is created deterministically')]
	public function testNamedEqual() {
		$id1 = new NamedIdentifier(NamedIdentifier::NAMESPACE_URL, 'https://smol.blog/post/123');
		$id2 = new NamedIdentifier(NamedIdentifier::NAMESPACE_URL, 'https://smol.blog/post/123');
		$this->assertEquals($id1, $id2);
	}

	#[TestDox('Identifiers will serialize to and deserialize from a string')]
	public function testSerialization() {
		$idString = '10a353e4-0ccf-5f74-a77b-067262bfc588';
		$idObject = new NamedIdentifier(NamedIdentifier::NAMESPACE_URL, 'https://smol.blog/post/123');

		$this->assertEquals($idString, $idObject->toString());
		$this->assertEquals(strval($idObject), strval(Identifier::fromString($idString)));
	}

	#[TestDox('Identifiers will serialize to and deserialize from a byte-compressed string')]
	public function testByteSerialization() {
		$idObject = new NamedIdentifier(NamedIdentifier::NAMESPACE_URL, 'https://smol.blog/post/123');
		$byteString = hex2bin('10a353e40ccf5f74a77b067262bfc588');

		$this->assertEquals($byteString, $idObject->toByteString());
		$this->assertEquals(strval($idObject), strval(Identifier::fromByteString($byteString)));
	}

	#[TestDox('It will throw an exception if it can\'t deserialize the string')]
	public function testStringException() {
		$this->expectException(InvalidValueProperties::class);

		Identifier::fromString('not-an-id');
	}

	#[TestDox('It will throw an exception if it can\'t deserialize the byte-compressed string')]
	public function testByteException() {
		$this->expectException(InvalidValueProperties::class);

		Identifier::fromByteString(hex2bin('10a353e40ccf5f74a77b067262bfc58888'));
	}

	#[TestDox('Identifier::nil() will return the nil UUID')]
	public function testNil() {
		$this->assertEquals('00000000-0000-0000-0000-000000000000', strval(Identifier::nil()));
	}
}
