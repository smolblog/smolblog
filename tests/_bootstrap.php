<?php

namespace Smolblog\Test;

use Smolblog\Core\Endpoint\{Endpoint, EndpointConfig, EndpointRequest, EndpointResponse};

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
