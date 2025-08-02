<?php

namespace Smolblog\Foundation\Value\Traits;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Test\TestCase;

readonly abstract class ExampleSupertype extends Value implements SerializableValue {
	use SerializableSupertypeKit;
	public function __construct(public string $one) {}
}

readonly abstract class ExampleSupertypeWithFallback extends Value implements SerializableValue {
	use SerializableSupertypeKit;
	public function __construct(public string $one) {}
	public static function getFallbackClass(): ?string { return UnknownSubtype::class; }
}

final readonly class ExampleSubtype extends ExampleSupertype {
	public function __construct(string $one, public string $two) { parent::__construct($one); }
}

final readonly class ExampleSubtypeWithFallback extends ExampleSupertypeWithFallback {
	public function __construct(string $one, public string $two) { parent::__construct($one); }
}

final readonly class UnknownSubtype extends ExampleSupertypeWithFallback {
	public function __construct(string $one, public string $original) { parent::__construct($one); }
	public static function deserializeValue(array $data): static {
		return new static(one: $data['one'] ?? 'n/a', original: $data['type'] ?? 'Unknown');
	}
}

#[CoversTrait(SerializableSupertypeKit::class)]
final class SerializableSupertypeTest extends TestCase {
	#[TestDox('It will add a type field to the serialization and deserialize without it.')]
	public function testSerialization() {
		$object = new ExampleSubtype(one: 'two', two: 'four');
		$array = ['one' => 'two', 'two' => 'four', 'type' => ExampleSubtype::class];

		$this->assertEquals($object, ExampleSubtype::deserializeValue($array));
		$this->assertEquals($array, $object->serializeValue());
	}

	#[TestDox('It will add a type field to the reflection for the supertype only.')]
	public function testReflection() {
		$supertype = ExampleSupertype::reflection();
		$subtype = ExampleSubtype::reflection();

		$this->assertArrayHasKey('type', $supertype);
		$this->assertArrayNotHasKey('type', $subtype);
	}

	#[TestDox('It will return an empty reflection if it is not a Value.')]
	public function testEmptyReflection() {
		$someOtherClass = new class() {
			use SerializableSupertypeKit;
		};

		$this->assertEmpty(get_class($someOtherClass)::reflection());
	}

	public static function knownTypes(): array {
		return [
			'the test subclass' => [
				new ExampleSubtype(one: 'two', two: 'four')
			],
			'an anonymous subclass' => [
				new readonly class(one: 'two', three: 'four') extends ExampleSupertype {
					public function __construct(string $one, public string $three) {
						parent::__construct($one);
					}
				}
			],
		];
	}

	#[DataProvider('knownTypes')]
	#[TestDox('It will deserialize $_dataName to its original class.')]
	public function testKnownEvents(ExampleSupertype $object) {
		$deserialized = ExampleSupertype::deserializeValue($object->serializeValue());

		$this->assertInstanceOf(get_class($object), $deserialized);
		$this->assertEquals($object->serializeValue(), $deserialized->serializeValue());
	}

	public static function unknownTypes(): array {
		$anon = new readonly class(one: 'two', five: 'three') extends Value implements SerializableValue {
			use SerializableValueKit;
			public function __construct(public string $one, public string $five) {}
		};

		return [
			'a class that does not exist' => [
				'serialized' => [
					'one' => 'hello',
					'type' => 'ClassThatDoesNotExist',
				],
				'expected' => new UnknownSubtype(
					one: 'hello',
					original: 'ClassThatDoesNotExist'
				),
			],
			'a non-Supertype subclass' => [
				'serialized' => [
					...$anon->serializeValue(),
					'type' => get_class($anon),
				],
				'expected' => new UnknownSubtype(
					one: 'two',
					original: get_class($anon)
				),
			],
			'an object serialized without a type' => [
				'serialized' => [
					'one' => 'two',
					'three' => 'four',
				],
				'expected' => new UnknownSubtype(
					one: 'two',
					original: 'Unknown'
				),
			],
		];
	}

	#[DataProvider('unknownTypes')]
	#[TestDox('It will deserialize $_dataName to the given fallback.')]
	public function testUnknownEventsWithFallback(array $serialized, UnknownSubtype $expected) {
		$deserialized = ExampleSupertypeWithFallback::deserializeValue($serialized);

		// $this->assertInstanceOf(UnknownSubtype::class, $deserialized);
		$this->assertEquals($expected, $deserialized);
	}

	#[DataProvider('unknownTypes')]
	#[TestDox('It will throw an exception if no fallback is given.')]
	public function testUnknownEventsWithoutFallback(array $serialized, UnknownSubtype $expected) {
		$this->expectException(InvalidValueProperties::class);
		ExampleSupertype::deserializeValue($serialized);
	}
}
