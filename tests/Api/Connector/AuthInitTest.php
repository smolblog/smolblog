<?php

namespace Smolblog\Api\Connector;

use PHPUnit\Framework\TestCase;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Core\Connector\Commands\BeginAuthRequest;
use Smolblog\Core\Connector\Services\ConnectorRegistrar;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EndpointTestToolkit;

final class AuthInitTest extends TestCase {
	use EndpointTestToolkit;
	const ENDPOINT = AuthInit::class;

	public function testItRespondsToACorrectRequest() {
		$conReg = $this->createStub(ConnectorRegistrar::class);
		$conReg->method('has')->willReturn(true);

		$command = new BeginAuthRequest(
			provider: 'smolblog',
			userId: Identifier::fromString('77076e6e-e268-4935-9b88-8b3ea2b70d67'),
			callbackUrl: '//smol.blog/api/connect/callback/smolblog',
		);

		$endpoint = new AuthInit(
			bus: $this->mockBusExpects($command),
			connectors: $conReg,
			env: $this->getApiEnvironment(),
		);

		$endpoint->run(
			userId: Identifier::fromString('77076e6e-e268-4935-9b88-8b3ea2b70d67'),
			params: ['provider' => 'smolblog']
		);
	}

	public function testItGivesNotFoundOnMissingConnector() {
		$this->expectException(NotFound::class);

		$endpoint = new AuthInit(
			bus: $this->createStub(MessageBus::class),
			connectors: $this->createStub(ConnectorRegistrar::class),
			env: $this->getApiEnvironment(),
		);

		$endpoint->run(userId: Identifier::createRandom());
	}
}
