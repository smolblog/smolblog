<?php

namespace Smolblog\Api;

use PHPUnit\Framework\TestCase;

final class EndpointConfigTest extends TestCase {
	public function testItNormalizesTheRoute() {
		$std = '/route/one/two';

		$this->assertEquals($std, (new EndpointConfig('route/one/two'))->route);
		$this->assertEquals($std, (new EndpointConfig('/route/one/two'))->route);
		$this->assertEquals($std, (new EndpointConfig('route/one/two/'))->route);
		$this->assertEquals($std, (new EndpointConfig('/route/one/two/'))->route);
	}

	public function testItHasSaneDefualts() {
		$config = new EndpointConfig(route: '/one/two');

		$this->assertEquals(Verb::GET, $config->verb);
		$this->assertEquals([AuthScope::Read, AuthScope::Write], $config->requiredScopes);
	}
}
