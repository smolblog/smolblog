<?php
use PHPUnit\Framework\Attributes\CoversClass;
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

#[CoversClass(FieldKit::class)]
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
}
