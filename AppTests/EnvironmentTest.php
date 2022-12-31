<?php

namespace Smolblog\App;

use PHPUnit\Framework\TestCase;

final class EnvironmentTest extends TestCase {
	public function testAddsTrailingSlashToApiBase() {
		$envNoSlash = new Environment(apiBase: '//localhost');
		$this->assertEquals('//localhost/', $envNoSlash->apiBase);

		$envWithSlash = new Environment(apiBase: '//localhost/');
		$this->assertEquals('//localhost/', $envWithSlash->apiBase);
	}
}
