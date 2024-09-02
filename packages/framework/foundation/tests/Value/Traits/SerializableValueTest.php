<?php

namespace Smolblog\Foundation\Value\Traits;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Test\TestCase;

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

readonly class NonSerializableValueTest extends Value {
	public function __construct(public array $contents) {}
}

readonly class SerializedWithNonSerializedValueTest extends ValueTestBase {
	public function __construct(public NonSerializableValueTest $contents) {}
}

#[CoversTrait(SerializableValueKit::class)]
#[CoversClass(ArrayType::class)]
final class SerializableValueTest extends TestCase {
	public static function valueExamples(): array {
		return [
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
			]
		];
	}


	#[TestDox('will serialize to an array')]
	#[DataProvider('valueExamples')]
	public function testToArray(SerializableValue $object, array $array, string $json, array $info) {
		$this->assertEquals($array, $object->serializeValue());
	}

	#[TestDox('will serialize to JSON')]
	#[DataProvider('valueExamples')]
	public function testToJson(SerializableValue $object, array $array, string $json, array $info) {
		$this->assertEquals($json, $object->toJson());
		$this->assertEquals($json, json_encode($object));
	}

	#[TestDox('will deserialize from an array')]
	#[DataProvider('valueExamples')]
	public function testFromArray(SerializableValue $object, array $array, string $json, array $info) {
		$class = get_class($object);
		$this->assertEquals($object, $class::deserializeValue($array));
	}

	#[TestDox('will deserialize from JSON')]
	#[DataProvider('valueExamples')]
	public function testFromJson(SerializableValue $object, array $array, string $json, array $info) {
		$class = get_class($object);
		$this->assertEquals($object, $class::fromJson($json));
	}

	#[TestDox('provides property info for the class')]
	#[DataProvider('valueExamples')]
	public function testPropInfo(ValueTestBase $object, array $array, string $json, array $info) {
		$this->assertEquals($info, $object::getPropertyInfo());
	}

	#[TestDox('It will throw an exception when default serialization is used with a union type')]
	public function testExceptionOnUnionType() {
		$this->expectException(CodePathNotSupported::class);

		$value = new readonly class('one', 2, new SimpleValueTest('three')) extends ValueTestBase {
			public function __construct(
				public string $one,
				public int $two,
				public SimpleValueTest|ManyScalarsValueTest $three,
			) {}
		};
		$value->serializeValue();
	}

	#[TestDox('It will ignore fields that are not defined in the propertyInfo when serializing and deserializing.')]
	public function testIgnoreNotInPropertyInfo() {
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

		$this->assertJsonStringEqualsJsonString(
			'{"one":"one","two":2,"three":{"value":"three"}}',
			json_encode($value)
		);

		$jsonWithExtra = '{"one":"one","two":2,"three":{"value":"three"},"five":true}';
		$this->assertEquals($value, get_class($value)::fromJson($jsonWithExtra));
	}

	public function testAllObjectPropertiesMustImplementSerializableValueToSerialize() {
		$this->expectException(CodePathNotSupported::class);

		(new SerializedWithNonSerializedValueTest(new NonSerializableValueTest(['one', 'two'])))->serializeValue();
	}

	public function testAllObjectPropertiesMustImplementSerializableValueToDeserialize() {
		$this->expectException(CodePathNotSupported::class);

		SerializedWithNonSerializedValueTest::deserializeValue(['contents' => ['contents' => ['one', 'two']]]);
	}

	/*
	Test this fails I guess

			'a non-SerializableValue property' => [
				'object' => new SerializedWithNonSerializedValueTest(
					new NonSerializableValueTest([
						new SimpleValueTest('one'),
						new SimpleValueTest('two'),
						new SimpleValueTest('three'),
					])
				),
				'array' => ['contents' => ['contents' => [['value' => 'one'], ['value' => 'two'], ['value' => 'three']]]],
				'json' => '{"contents":{"contents":[{"value":"one"},{"value":"two"},{"value":"three"}]}}',
				'info' => ['contents' => NonSerializableValueTest::class],
			]
			*/
}
