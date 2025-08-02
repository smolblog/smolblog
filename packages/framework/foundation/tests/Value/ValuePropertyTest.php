<?php

namespace Smolblog\Foundation\Value;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\TestCase;

#[CoversClass(ValueProperty::class)]
final class ValuePropertyTest extends TestCase {
	public function testItWillInferADisplayNameIfNoneIsGiven() {
		$nameless = new ValueProperty(
			name: 'testValue',
			type: 'string',
		);
		$named = new ValueProperty(
			name: 'somethingSomethingId',
			type: 'string',
			displayName: 'Easy',
		);
		$errored = new ValueProperty(
			name: '',
			type: 'string',
		);

		$this->assertEquals('Test Value', $nameless->displayName);
		$this->assertEquals('Easy', $named->displayName);
		$this->assertEquals('', $errored->displayName);
	}

	public function testItRequiresAnItemsPropertyForArrays() {
		$this->expectException(InvalidValueProperties::class);

		new ValueProperty(
			name: 'testValue',
			type: 'array',
		);
	}

	public function testMaxConstraintCannotBeLessThanMin() {
		$this->expectException(InvalidValueProperties::class);

		new ValueProperty(
			name: 'testValue',
			type: 'int',
			max: 5,
			min: 10,
		);
	}
}
