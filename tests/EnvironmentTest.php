<?php

namespace Smolblog\Core;

use PHPUnit\Framework\TestCase;

final class EnvironmentTest extends TestCase {
	public function testArbitraryEnvionmentVariablesCanBeAddedAtBuildtime() {
		$env = new Environment(apiBase: '//localhost', someKey: 'someValue');

		$this->assertEquals('someValue', $env->someKey);
	}

	public function testUndefinedValuesGiveNull() {
		$env = new Environment(apiBase: '//localhost');

		$this->assertNull($env->undefinedValue);
	}
}
