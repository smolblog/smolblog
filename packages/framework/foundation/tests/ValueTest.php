<?php

namespace Smolblog\Foundation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Attributes\ArrayType;
use Smolblog\Foundation\Value\Attributes\DisplayName;
use Smolblog\Foundation\Value\Attributes\Target;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\ValueProperty;
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

	#[TestDox('Default getPropertyInfo() does not work with union types')]
	public function testPropertyUnionType() {
		$this->expectException(CodePathNotSupported::class);

		$class = new readonly class(543) extends Value {
			public function __construct(public string|int $thing) {}
		};

		get_class($class)::reflection();
	}

	#[TestDox('Default getPropertyInfo() requires typed arrays')]
	public function testPropertyNoArrayType() {
		$this->expectException(CodePathNotSupported::class);

		$class = new readonly class([543]) extends Value {
			public function __construct(public array $thing) {}
		};

		get_class($class)::reflection();
	}

	#[TestDox('reflection() will generate an appropriate array of ValueProperty objects.')]
	public function testReflection() {
		$class = new readonly class(
			stringVal: 'one',
			intVal: 2,
			id: $this->randomId(),
			stringList: [],
			stringMap: [],
			idList: [],
			idMap: [],
			mixedMap: [],
		) extends Value {
			public function __construct(
				public string $stringVal,
				#[DisplayName('Something')]public int $intVal,
				#[Target('\\OtherLibrary\\Entity')] public Identifier $id,
				#[ArrayType(ArrayType::TYPE_STRING)] public array $stringList,
				#[ArrayType(ArrayType::TYPE_STRING, isMap: true)] public array $stringMap,
				#[ArrayType(Identifier::class), Target('\\OtherLibrary\\Entity')] public array $idList,
				#[ArrayType(Identifier::class, isMap: true), Target('\\OtherLibrary\\Entity'), DisplayName('Something Else')] public array $idMap,
				#[ArrayType(ArrayType::NO_TYPE, isMap: true)] public array $mixedMap,
			) {}
		};

		$expected = [
			'stringVal' => new ValueProperty(
				name: 'stringVal',
				type: 'string',
				displayName: 'String Val',
			),
			'intVal' => new ValueProperty(
				name: 'intVal',
				type: 'int',
				displayName: 'Something',
			),
			'id' => new ValueProperty(
				name: 'id',
				type: Identifier::class,
				displayName: 'Id',
				target: '\\OtherLibrary\\Entity',
			),
			'stringList' => new ValueProperty(
				name: 'stringList',
				type: 'array',
				items: 'string',
				displayName: 'String List',
			),
			'stringMap' => new ValueProperty(
				name: 'stringMap',
				type: 'map',
				items: 'string',
				displayName: 'String Map',
			),
			'idList' => new ValueProperty(
				name: 'idList',
				type: 'array',
				items: Identifier::class,
				displayName: 'Id List',
				target: '\\OtherLibrary\\Entity',
			),
			'idMap' => new ValueProperty(
				name: 'idMap',
				type: 'map',
				items: Identifier::class,
				displayName: 'Something Else',
				target: '\\OtherLibrary\\Entity',
			),
			'mixedMap' => new ValueProperty(
				name: 'mixedMap',
				type: 'map',
				displayName: 'Mixed Map',
			),
		];

		$actual = get_class($class)::reflection();

		$this->assertEquals($expected, $actual);
	}
}
