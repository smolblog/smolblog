<?php

namespace Smolblog\Core\Endpoints;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Environment;
use Smolblog\Core\Factories\TransientFactory;
use Smolblog\Core\Registrars\ConnectorRegistrar;
use Smolblog\Test\EndpointTestToolkit;

final class ConnectInitTest extends TestCase {
	use EndpointTestToolkit;

	public function setUp(): void {
		$environment = $this->createStub(Environment::class);
		$connectors = $this->createStub(ConnectorRegistrar::class);
		$transients = $this->createStub(TransientFactory::class);

		$this->endpoint = new ConnectInit(
			env: $environment,
			connectors: $connectors,
			transients: $transients,
		);
	}
}
