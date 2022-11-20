<?php

namespace Smolblog\Framework;

use PHPUnit\Framework\TestCase;

final class ConcreteExtendableValue extends ExtendableValue {
	public function __construct(
		public readonly string $definedKey,
		mixed ...$extended
	) {
		parent::__construct(...$extended);
	}
}

final class ExtendableValueTest extends TestCase {
	public function testSettingDefinedPropertyGivesError() {
		$this->expectError();
		$cev = new ConcreteExtendableValue(definedKey: 'dictionary', someKey: 'someValue');

		$cev->definedKey = 'nope';
	}

	public function testSettingRuntimePropertyGivesError() {
		$this->expectError();
		$cev = new ConcreteExtendableValue(definedKey: 'dictionary', someKey: 'someValue');

		$cev->someKey = 'nope';
	}

	public function testArbitraryVariablesCanBeAddedAtBuildtime() {
		$cev = new ConcreteExtendableValue(definedKey: 'dictionary', someKey: 'someValue');

		$this->assertEquals('dictionary', $cev->definedKey);
		$this->assertEquals('someValue', $cev->someKey);
	}

	public function testUndefinedValuesGiveNull() {
		$cev = new ConcreteExtendableValue(definedKey: 'dictionary', someKey: '//localhost');

		$this->assertNull($cev->undefinedValue);
	}

	public function testItSerializesToArray() {
		$cev = new ConcreteExtendableValue(definedKey: 'dictionary', someKey: 'someValue');

		$this->assertEquals(
			['definedKey' => 'dictionary', 'someKey' => 'someValue'],
			$cev->toArray()
		);
	}

	public function testItSerializesToJson() {
		$cev = new ConcreteExtendableValue(definedKey: 'dictionary', someKey: 'someValue');

		$this->assertJsonStringEqualsJsonString(
			'{"definedKey":"dictionary","someKey":"someValue"}',
			json_encode($cev)
		);
	}

	public function testCanBeCreatedFromSerializedArray() {
		$actual = ConcreteExtendableValue::fromArray(['definedKey' => 'dictionary', 'someKey' => 'someValue']);

		$this->assertEquals('dictionary', $actual->definedKey);
		$this->assertEquals('someValue', $actual->someKey);
	}

	public function testCanBeCreatedFromSerializedJson() {
		$actual = ConcreteExtendableValue::jsonDeserialize('{"definedKey":"dictionary","someKey":"someValue"}');

		$this->assertEquals('dictionary', $actual->definedKey);
		$this->assertEquals('someValue', $actual->someKey);
	}
}
