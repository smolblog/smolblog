<?php

namespace Smolblog\Foundation\Value\Fields;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\TestCase;

#[CoversClass(Email::class)]
final class EmailTest extends TestCase {
	#[TestDox('It stores an email string')]
	public function testRandom() {
		$this->assertInstanceOf(Email::class, new Email('snek@smol.blog'));
	}

	#[TestDox('It will serialize to and deserialize from a string')]
	public function testSerialization() {
		$emailString = 'snek@smol.blog';
		$emailObject = new Email('snek@smol.blog');

		$this->assertEquals($emailString, $emailObject->toString());
		$this->assertEquals(strval($emailObject), strval(Email::fromString($emailString)));
	}

	#[TestDox('It will throw an exception if it is not a valid email')]
	public function testStringException() {
		$this->expectException(InvalidValueProperties::class);

		new Email('not-an-email');
	}
}
