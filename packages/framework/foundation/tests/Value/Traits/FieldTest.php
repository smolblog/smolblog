<?php

use PHPUnit\Framework\Attributes\CoversTrait;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Field;
use Smolblog\Foundation\Value\Traits\FieldKit;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;
use Smolblog\Test\TestCase;

final readonly class ExampleField extends Value implements Field {
	use FieldKit;
	public function __construct(public string $one, public string $two) {}
	public function toString(): string { return "$this->one|$this->two"; }
	public static function fromString(string $data): static { return new ExampleField(...explode('|', $data)); }
}

final readonly class TestValueWithField extends Value implements SerializableValue {
	use SerializableValueKit;
	public function __construct(public string $name, public int $idNumber, public ExampleField $meta) {}
}

#[CoversTrait(FieldKit::class)]
final class FieldTest extends TestCase {
	public function testItWillSerializeToAndDeserializeFromTheStringRepresentation() {
		$object = new ExampleField(one: 'abc', two: 'xyz');
		$string = 'abc|xyz';

		$this->assertEquals($string, $object->serializeValue());
		$this->assertEquals($object, ExampleField::deserializeValue($string));
	}

	public function testItUsesTheStringRepresentationAsTheStringableValue() {
		$this->assertEquals('abc|xyz', strval(new ExampleField(one: 'abc', two: 'xyz')));
	}

	public function testItWillSerializeAsPartOfASerializableValueObject() {
		$meta = new ExampleField(one: 'abc', two: 'xyz');
		$value = new TestValueWithField(name: 'Bob', idNumber: 5, meta: $meta);
		$json = '{"name":"Bob","idNumber":5,"meta":"abc|xyz"}';

		$this->assertEquals($json, json_encode($value));
		$this->assertEquals($value, TestValueWithField::fromJson($json));
	}

	public function testTwoIdenticalFieldObjectsAreEqual() {
		$one = new ExampleField(one: 'abc', two: 'xyz');
		$two = new ExampleField(one: 'abc', two: 'xyz');

		$this->assertTrue($one->equals($two));
		$this->assertObjectEquals($one, $two);
	}

	public function testTwoDifferentFieldObjectsWithTheSameSerializedValueAreEqual() {
		$one = new ExampleField(one: 'abc', two: 'xyz');
		$two = new readonly class('abc|xyz') extends Value implements Field {
			use FieldKit;
			public function __construct(public string $val) {}
			public function toString(): string { return $this->val; }
			public static function fromString(string $string): static { return new self($string); }
		};

		$this->assertNotInstanceOf(ExampleField::class, $two);
		$this->assertTrue($one->equals($two));
		$this->assertObjectEquals($one, $two);
	}

	public function testTwoFieldObjectsWithDifferentSerializedValuesAreNotEqual() {
		$one = new ExampleField(one: 'abc', two: 'xyz');
		$two = new ExampleField(one: 'xyz', two: 'xyz');

		$this->assertFalse($one->equals($two));
		$this->assertObjectNotEquals($one, $two);
	}

	public function testAFieldIsNotEqualToAValueThatIsNotAField() {
		$one = new ExampleField(one: 'abc', two: 'xyz');
		$two = new TestValueWithField(name: 'Bob', idNumber: 5, meta: $one);

		$this->assertFalse($one->equals($two));
		$this->assertObjectNotEquals($one, $two);
	}
}
