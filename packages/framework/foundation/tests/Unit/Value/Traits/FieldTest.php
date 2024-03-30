<?php
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Field;
use Smolblog\Foundation\Value\Traits\FieldKit;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

final readonly class TestField extends Value implements Field {
	use FieldKit;
	public function __construct(public string $one, public string $two) {}
	public function toString(): string { return "$this->one|$this->two"; }
	public static function fromString(string $data): static { return new TestField(...explode('|', $data)); }
}

final readonly class TestValueWithField extends Value implements SerializableValue {
	use SerializableValueKit;
	public function __construct(public string $name, public int $idNumber, public TestField $meta) {}
}

describe('FieldKit::toArray, ::fromArray', function() {
	it('gives the string representation as the serialized value', fn() =>
		expect((new TestField(one: 'abc', two: 'xyz'))->toArray())->toBe('abc|xyz')
	);

	it('uses the string representation to deserialize', function() {
		$actual = TestField::fromArray('abc|xyz');
		expect($actual)->toBeInstanceOf(TestField::class);
		expect($actual->one)->toBe('abc');
		expect($actual->two)->toBe('xyz');
	});
});

describe('FieldKit::__toString', function() {
	it('uses the string representation as the Stringable value', fn() =>
		expect(strval(new TestField(one: 'abc', two: 'xyz')))->toBe('abc|xyz')
	);
});

describe('FieldKit + SerializableValueKit', function() {
	$meta = new TestField(one: 'abc', two: 'xyz');
	$value = new TestValueWithField(name: 'Bob', idNumber: 5, meta: $meta);
	$json = '{"name":"Bob","idNumber":5,"meta":"abc|xyz"}';

	it('will serialize a field as part of a SerializableValue', fn() =>
		expect(json_encode($value))->toBe($json)
	);

	it('will deserialize a field as part of a SerializableValue', fn() =>
		expect(TestValueWithField::fromJson($json))->toMatchValue($value)
	);
});
