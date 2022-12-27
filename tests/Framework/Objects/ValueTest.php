<?php

namespace Smolblog\Framework\Objects;

use PHPUnit\Framework\TestCase;

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
	public function testSettingDefinedPropertyGivesError() {
		$this->expectError();
		$val = new ConcreteValue(key: '//localhost');

		$val->key = 'nope';
	}

	public function testSettingRuntimePropertyGivesError() {
		$this->expectError();
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

	public function testCanBeModifiedByCopying() {
		$first = new ConcreteValue(key: 'old');
		$second = $first->newWith(key: 'new');

		$this->assertEquals('old', $first->key);
		$this->assertEquals('new', $second->key);
	}

	public function testAComplexValueCanBeModifiedByCopying() {
		$first = new ComplexValue(key: 'old', other: new ConcreteValue(key: 'other'));
		$second = $first->newWith(key: 'new');

		$this->assertEquals('old', $first->key);
		$this->assertEquals('new', $second->key);
		$this->assertEquals('other', $first->other->key);
		$this->assertEquals('other', $second->other->key);
	}
}
