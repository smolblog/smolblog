<?php

namespace Smolblog\Framework\Objects;

use Exception;
use Smolblog\Test\TestCase;

final class ConcreteValue extends Value {
	public function __construct(
		public readonly string $key
	) {
	}
}

final class ComplexValue extends Value {
	public function __construct(
		public readonly string $key,
		public readonly ConcreteValue $other,
	) {
	}
}

final class ValueTest extends TestCase {
	public function testSettingRuntimePropertyGivesError() {
		$this->expectException(Exception::class);
		$val = new ConcreteValue(key: '//localhost');

		$val->someKey = 'nope';
	}

	public function testItSerializesToArray() {
		$val = new ConcreteValue(key: 'dictionary');

		$this->assertEquals(
			['key' => 'dictionary'],
			$val->toArray()
		);
	}

	public function testItSerializesToJson() {
		$val = new ConcreteValue(key: 'dictionary');

		$this->assertJsonStringEqualsJsonString(
			'{"key":"dictionary"}',
			json_encode($val)
		);
	}

	public function testCanBeCreatedFromSerializedArray() {
		$actual = ConcreteValue::fromArray(['key' => 'arrayed']);

		$this->assertEquals('arrayed', $actual->key);
	}

	public function testCanBeCreatedFromSerializedJson() {
		$actual = ConcreteValue::jsonDeserialize('{"key":"Jason"}');

		$this->assertEquals('Jason', $actual->key);
	}
}
