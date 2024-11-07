<?php

namespace Smolblog\Foundation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\TestCase;

#[CoversClass(Value::class)]
#[CoversClass(InvalidValueProperties::class)]
final class ValueTest extends TestCase {
	#[TestDox('with() creates a new object')]
	public function testWithCreatesNew() {
		$first = new readonly class('world') extends Value {
			public function __construct(public string $hello) {}
		};
		$second = $first->with();

		$this->assertInstanceOf(get_class($first), $second);
		$this->assertEquals($second->hello, $first->hello);
		$this->assertNotSame($second, $first);
	}

	#[TestDox('with() will replace the given fields')]
	public function testWithReplacesGiven() {
		$first = new readonly class('one', 'five') extends Value {
			public function __construct(public string $one, public string $three) {}
		};
		$second = $first->with(three: 'three');

		$this->assertEquals('one', $first->one);
		$this->assertEquals('one', $second->one);
		$this->assertEquals('five', $first->three);
		$this->assertEquals('three', $second->three);
	}

	#[TestDox('with() will ignore private values')]
	public function testWithIgnoresPrivate() {
		$first = new readonly class('given', 'given') extends Value {
			public function __construct(public string $public = 'default', private string $private = 'default') {}
			public function getPrivate() { return $this->private; }
		};
		$second = $first->with();

		$this->assertEquals('given', $first->public);
		$this->assertEquals('given', $second->public);
		$this->assertEquals('given', $first->getPrivate());
		$this->assertEquals('default', $second->getPrivate());
	}

	#[TestDox('with() will throw an exception on error')]
	public function testWithThrowsException() {
		$this->expectException(InvalidValueProperties::class);

		$first = new readonly class('camelot') extends Value {
			public function __construct(public string $camelot) {}
		};
		$first->with(itIsOnly: 'a model');
	}

	#[TestDox('equals() will return true if the objects\' class and values match')]
	public function testEqualsClassAndValuesMatch() {
		$first = new readonly class('camelot') extends Value {
			public function __construct(public string $destination) {}
		};
		$second = new (\get_class($first))('camelot');

		$this->assertEquals($first->destination, $second->destination);
		$this->assertTrue($first->equals($second));
		$this->assertObjectEquals($first, $second);
	}

	#[TestDox('equals() will return false if the objects\' values do not match')]
	public function testEqualsValueMismatch() {
		$first = new readonly class('camelot') extends Value {
			public function __construct(public string $destination) {}
		};
		$second = new (\get_class($first))('a model');

		$this->assertNotEquals($first->destination, $second->destination);
		$this->assertFalse($first->equals($second));
		$this->assertObjectNotEquals($first, $second);
	}

	#[TestDox('equals() will return false if the objects\' classes do not match')]
	public function testEqualsClassMismatch() {
		$first = new readonly class('camelot') extends Value {
			public function __construct(public string $destination) {}
		};
		$second = new readonly class('camelot') extends Value {
			public function __construct(public string $destination) {}
		};

		$this->assertEquals($first->destination, $second->destination);
		$this->assertFalse($first->equals($second));
	}
}
