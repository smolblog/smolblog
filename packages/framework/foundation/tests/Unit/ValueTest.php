<?php

use Smolblog\Framework\Foundation\Value;
use Smolblog\Framework\Foundation\Attributes\ArrayType;

readonly class ValueTestBase extends Value {
	public static function getPropertyInfo(): array {
		return self::propertyInfo();
	}
}

readonly class SimpleValueTest extends ValueTestBase {
	public function __construct(public string $value) {}
}

readonly class ManyScalarsValueTest extends ValueTestBase {
	public function __construct(public string $one, public int $two, public bool $three) {}
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

it('can be used as the base for a simple value', function() {
	$value = new SimpleValueTest('hello');
	expect($value->value)->toBe('hello');
});

it('will serialize to an array', function(Value $object, array $array, string $json) {
	expect($object->serialize())->toEqual($array);
	expect(json_encode($object))->toEqual($json);
})->with('valueExamples');

it('will deserialize from an array', function(Value $object, array $array, string $json) {
	$class = get_class($object);
	expect($class::deserialize($array))->toEqual($object);
	expect($class::jsonDeserialize($json))->toEqual($object);
})->with('valueExamples');

test('the class provides the expected property info', function(string $class, array $expected) {
	expect($class::getPropertyInfo())->toEqual($expected);
})->with('classExamples');

dataset('valueExamples', [
	'simple' => [
		new SimpleValueTest('hello'),
		['value' => 'hello'],
		'{"value":"hello"}',
	],
	'many scalars' => [
		new ManyScalarsValueTest('one', 2, true),
		['one' => 'one', 'two' => 2, 'three' => true],
		'{"one":"one","two":2,"three":true}',
	],
	'recursive' => [
		new RecursiveValueTest(new SimpleValueTest('inside')),
		['outside' => ['value' => 'inside']],
		'{"outside":{"value":"inside"}}',
	],
	'array with scalars' => [
		new ArrayWithScalarsValueTest(['one', 'two', 'three']),
		['array' => ['one', 'two', 'three']],
		'{"array":["one","two","three"]}',
	],
	'array with objects' => [
		new ArrayWithObjectsValueTest([
			new SimpleValueTest('one'),
			new SimpleValueTest('two'),
			new SimpleValueTest('three'),
		]),
		['array' => [['value' => 'one'], ['value' => 'two'], ['value' => 'three']]],
		'{"array":[{"value":"one"},{"value":"two"},{"value":"three"}]}',
	],
]);

dataset('classExamples', [
	'simple' => [
		SimpleValueTest::class,
		['value' => null],
	],
	'many scalars' => [
		ManyScalarsValueTest::class,
		['one' => null, 'two' => null, 'three' => null],
	],
	'recursive' => [
		RecursiveValueTest::class,
		['outside' => SimpleValueTest::class],
	],
	'array with scalars' => [
		ArrayWithScalarsValueTest::class,
		['array' => null],
	],
	'array with objects' => [
		ArrayWithObjectsValueTest::class,
		['array' => new ArrayType(SimpleValueTest::class)],
	],
]);
