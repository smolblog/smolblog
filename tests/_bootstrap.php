<?php

namespace Smolblog\Test;

use Smolblog\App\Endpoint\{Endpoint, EndpointConfig, EndpointRequest, EndpointResponse};
use Smolblog\Framework\Objects\Identifier;

require_once __DIR__ . '/../vendor/autoload.php';

trait EndpointTestToolkit {
	protected $endpoint;

	public function testItGivesAValidConfiguration(): void {
		$config = get_class($this->endpoint)::config();
		$this->assertInstanceOf(EndpointConfig::class, $config);
	}

	public function testItCanBeInstantiated(): void {
		$this->assertInstanceOf(Endpoint::class, $this->endpoint);
	}

	public function testItCanBeCalled(): void {
		$response = $this->endpoint->run(new EndpointRequest());
		$this->assertInstanceOf(EndpointResponse::class, $response);
	}
}

trait DateIdentifierTestKit {
	/**
	 * Asserts that two identifiers are created from the same date. A v7 UUID hashes the date, then adds random bytes.
	 * This function trims the random bytes and compares the remaining data.
	 */
	private function assertIdentifiersHaveSameDate(Identifier $expected, Identifier $actual) {
		$expectedTrim = substr(strval($expected), offset: 0, length: -8);
		$actualTrim = substr(strval($actual), offset: 0, length: -8);

		$this->assertEquals($expectedTrim, $actualTrim);
	}
}
