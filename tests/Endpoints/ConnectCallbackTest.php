<?php

namespace Smolblog\Core\Endpoints;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Factories\TransientFactory;
use Smolblog\Core\Registrars\ConnectorRegistrar;

final class ConnectCallbackTest extends TestCase {
	private $connectCallback;
	public function setUp(): void {
		$connectors = $this->createStub(ConnectorRegistrar::class);
		$transients = $this->createStub(TransientFactory::class);

		$this->connectCallback = new ConnectCallback(
			connectors: $connectors,
			transients: $transients,
		);
	}

	public function testItCanBeInstantiated(): void {
		$this->assertInstanceOf(ConnectCallback::class, $this->connectCallback);
	}
}
