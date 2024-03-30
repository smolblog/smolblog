<?php

use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\ArrayType;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

readonly class ValueTestBase extends Value implements SerializableValue {
	use SerializableValueKit;
	public static function getPropertyInfo(): array {
		return static::propertyInfo();
	}
}

readonly class SimpleValueTest extends ValueTestBase {
	public function __construct(public string $value) {}
}

readonly class ManyScalarsValueTest extends ValueTestBase {
	public function __construct(public string $one, public ?int $two = null, public ?bool $three = null) {}
}

readonly class RecursiveValueTest extends ValueTestBase {
	public function __construct(public SimpleValueTest $outside) {}
}

readonly class ArrayWithScalarsValueTest extends ValueTestBase {
	public function __construct(public array $array) {}
}

readonly class ArrayWithObjectsValueTest extends ValueTestBase {
	public function __construct(#[ArrayType(SimpleValueTest::class)] public array $array) {}
}

readonly class PrivatePropertyValueTest extends ValueTestBase {
	private string $private;
	protected string $protected;
	public function __construct(public string $public) {
		$this->private = 'private';
		$this->protected = 'protected';
	}
}

readonly class OverriddenPropertyInfoValueTest extends ValueTestBase {
	public function __construct(public string $one, public int $two, public Value $three) {}
	public static function propertyInfo(): array {
		$base = parent::propertyInfo();
		$base['three'] = SimpleValueTest::class;
		return $base;
	}
}

dataset('valueExamples', [
	'simple' => [
		'object' => new SimpleValueTest('hello'),
		'array' => ['value' => 'hello'],
		'json' => '{"value":"hello"}',
		'info' => ['value' => null],
	],
	'many scalars' => [
		'object' => new ManyScalarsValueTest('one', 2, true),
		'array' => ['one' => 'one', 'two' => 2, 'three' => true],
		'json' => '{"one":"one","two":2,"three":true}',
		'info' => ['one' => null, 'two' => null, 'three' => null],
	],
	'many scalars with nulls' => [
		'object' => new ManyScalarsValueTest('one'),
		'array' => ['one' => 'one'],
		'json' => '{"one":"one"}',
		'info' => ['one' => null, 'two' => null, 'three' => null],
	],
	'recursive' => [
		'object' => new RecursiveValueTest(new SimpleValueTest('inside')),
		'array' => ['outside' => ['value' => 'inside']],
		'json' => '{"outside":{"value":"inside"}}',
		'info' => ['outside' => SimpleValueTest::class],
	],
	'array with scalars' => [
		'object' => new ArrayWithScalarsValueTest(['one', 'two', 'three']),
		'array' => ['array' => ['one', 'two', 'three']],
		'json' => '{"array":["one","two","three"]}',
		'info' => ['array' => null],
	],
	'array with objects' => [
		'object' => new ArrayWithObjectsValueTest([
			new SimpleValueTest('one'),
			new SimpleValueTest('two'),
			new SimpleValueTest('three'),
		]),
		'array' => ['array' => [['value' => 'one'], ['value' => 'two'], ['value' => 'three']]],
		'json' => '{"array":[{"value":"one"},{"value":"two"},{"value":"three"}]}',
		'info' => ['array' => new ArrayType(SimpleValueTest::class)],
	],
	'private property' => [
		'object' => new PrivatePropertyValueTest('hello'),
		'array' => ['public' => 'hello'],
		'json' => '{"public":"hello"}',
		'info' => ['public' => null],
	],
	'overridden property info' => [
		'object' => new OverriddenPropertyInfoValueTest('one', 2, new SimpleValueTest('three')),
		'array' => ['one' => 'one', 'two' => 2, 'three' => ['value' => 'three']],
		'json' => '{"one":"one","two":2,"three":{"value":"three"}}',
		'info' => ['one' => null, 'two' => null, 'three' => SimpleValueTest::class],
	],
]);

describe('SerializableValueKit::toArray', function() {
	it('will serialize to an array', function(SerializableValue $object, array $array, string $json) {
		expect($object->toArray())->toEqual($array);
	})->with('valueExamples');

	it('will serialize to JSON', function(SerializableValue $object, array $array, string $json) {
		expect($object->toJson())->toEqual($json);
		expect(json_encode($object))->toEqual($json);
	})->with('valueExamples');

	it('will throw an exception when default serialization is used with a union type', function() {
		$value = new readonly class('one', 2, new SimpleValueTest('three')) extends ValueTestBase {
			public function __construct(
				public string $one,
				public int $two,
				public SimpleValueTest|ManyScalarsValueTest $three,
			) {}
		};

		expect(fn() => $value->toArray())->toThrow(CodePathNotSupported::class);
	});
});

describe('SerializableValueKit::fromArray', function() {
	it('will deserialize from an array', function(SerializableValue $object, array $array, string $json) {
		$class = get_class($object);
		expect($class::fromArray($array))->toEqual($object);
	})->with('valueExamples');

	it('will deserialize from JSON', function(SerializableValue $object, array $array, string $json) {
		$class = get_class($object);
		expect($class::fromJson($json))->toEqual($object);
	})->with('valueExamples');
});

describe('SerializableValueKit::propertyInfo', function() {
	it('provides property info for the class',
		function(ValueTestBase $object, array $_1, string $_2, array $info) {
			expect($object::getPropertyInfo())->toEqual($info);
		}
	)->with('valueExamples');

	it('will ignore fields that are not defined in the propertyInfo when serializing and deserializing', function() {
		$value = new readonly class('one', 2, new SimpleValueTest('three')) extends ValueTestBase {
			public function __construct(
				public string $one,
				public int $two,
				public Value $three,
				public bool $five = false,
			) {}
			public static function propertyInfo(): array {
				return [
					'one' => null,
					'two' => null,
					'three' => SimpleValueTest::class,
				];
			}
		};

		expect(json_encode($value))->toEqual('{"one":"one","two":2,"three":{"value":"three"}}');

		$jsonWithExtra = '{"one":"one","two":2,"three":{"value":"three"},"five":true}';
		expect(get_class($value)::fromJson($jsonWithExtra))->toEqual($value);
	});
});
