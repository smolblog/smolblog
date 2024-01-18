<?php

namespace Smolblog\Framework\Objects;

use Exception;
use Smolblog\Test\TestCase;

final class ConcreteExtendableValue extends Value {
	use ExtendableValueKit;

	public function __construct(
		public readonly string $definedKey,
		mixed ...$extended
	) {
		$this->extendedFields = $extended;
	}
}

final class ExtendableValueKitTest extends TestCase {
	public function testArbitraryValuesAreNotRequired() {
		$cev = new ConcreteExtendableValue(definedKey: 'dictionary');

		$this->assertEquals('dictionary', $cev->definedKey);
	}

	public function testSettingRuntimePropertyGivesError() {
		$this->expectException(Exception::class);
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
