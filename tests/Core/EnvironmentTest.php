<?php

namespace Smolblog\App;

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

	public function testSettingStandardPropertyGivesError() {
		$this->expectError();
		$env = new Environment(apiBase: '//localhost');

		$env->apiBase = 'nope';
	}

	public function testSettingRuntimePropertyGivesError() {
		$this->expectError();
		$env = new Environment(apiBase: '//localhost', someKey: 'someValue');

		$env->someKey = 'nope';
	}

	public function testSerializesToJson() {
		$env = new Environment(apiBase: '//localhost/', someKey: 'someValue');

		$this->assertJsonStringEqualsJsonString(
			'{"apiBase":"\/\/localhost/","someKey":"someValue"}',
			json_encode($env)
		);
	}

	public function testAddsTrailingSlashToApiBase() {
		$envNoSlash = new Environment(apiBase: '//localhost');
		$this->assertEquals('//localhost/', $envNoSlash->apiBase);

		$envWithSlash = new Environment(apiBase: '//localhost/');
		$this->assertEquals('//localhost/', $envWithSlash->apiBase);
	}
}
