<?php

namespace Smolblog\Api\Connector;

use PHPUnit\Framework\TestCase;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Core\Connector\Commands\FinishAuthRequest;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Core\Connector\Services\AuthRequestStateRepo;
use Smolblog\Core\Connector\Services\ConnectorRegistrar;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Test\EndpointTestToolkit;

final class AuthCallbackTest extends TestCase {
	use EndpointTestToolkit;
	const ENDPOINT = AuthCallback::class;

	public function testItRespondsToAnOAuth2Request() {
		$conReg = $this->createStub(ConnectorRegistrar::class);
		$conReg->method('has')->willReturn(true);

		$reqRep = $this->createStub(AuthRequestStateRepo::class);
		$reqRep->method('getAuthRequestState')->willReturn($this->createStub(AuthRequestState::class));

		$command = new FinishAuthRequest(
			provider: 'smolblog',
			stateKey: 'ea0e642a-6220-47db-92f4-7bac1d158079',
			code: 'acb72fd6-3e86-400f-8c2b-8b241fc3ad67'
		);

		$endpoint = new AuthCallback(
			bus: $this->mockBusExpects($command),
			connectors: $conReg,
			authRepo: $reqRep
		);

		$endpoint->run(
			params: [
				'provider' => 'smolblog',
				'state' => 'ea0e642a-6220-47db-92f4-7bac1d158079',
				'code' => 'acb72fd6-3e86-400f-8c2b-8b241fc3ad67',
			]
		);
	}

	public function testItRespondsToAnOAuth1Request() {
		$conReg = $this->createStub(ConnectorRegistrar::class);
		$conReg->method('has')->willReturn(true);

		$reqRep = $this->createStub(AuthRequestStateRepo::class);
		$reqRep->method('getAuthRequestState')->willReturn($this->createStub(AuthRequestState::class));

		$command = new FinishAuthRequest(
			provider: 'smolblog',
			stateKey: 'ea0e642a-6220-47db-92f4-7bac1d158079',
			code: 'acb72fd6-3e86-400f-8c2b-8b241fc3ad67'
		);

		$endpoint = new AuthCallback(
			bus: $this->mockBusExpects($command),
			connectors: $conReg,
			authRepo: $reqRep
		);

		$endpoint->run(
			params: [
				'provider' => 'smolblog',
				'oauth_token' => 'ea0e642a-6220-47db-92f4-7bac1d158079',
				'oauth_verifier' => 'acb72fd6-3e86-400f-8c2b-8b241fc3ad67',
			]
		);
	}

	public function testItGivesNotFoundOnMissingConnector() {
		$this->expectException(NotFound::class);

		$endpoint = new AuthCallback(
			bus: $this->createStub(MessageBus::class),
			connectors: $this->createStub(ConnectorRegistrar::class),
			authRepo: $this->createStub(AuthRequestStateRepo::class)
		);

		$endpoint->run();
	}

	public function testItGivesBadRequestWithNoState() {
		$this->expectException(BadRequest::class);

		$conReg = $this->createStub(ConnectorRegistrar::class);
		$conReg->method('has')->willReturn(true);

		$reqRep = $this->createStub(AuthRequestStateRepo::class);
		$reqRep->method('getAuthRequestState')->willReturn($this->createStub(AuthRequestState::class));

		$endpoint = new AuthCallback(
			bus: $this->createStub(MessageBus::class),
			connectors: $conReg,
			authRepo: $reqRep
		);

		$endpoint->run(
			params: [
				'provider' => 'smolblog',
				'code' => 'acb72fd6-3e86-400f-8c2b-8b241fc3ad67',
			]
		);
	}

	public function testItGivesBadRequestWithNoCode() {
		$this->expectException(BadRequest::class);

		$conReg = $this->createStub(ConnectorRegistrar::class);
		$conReg->method('has')->willReturn(true);

		$reqRep = $this->createStub(AuthRequestStateRepo::class);
		$reqRep->method('getAuthRequestState')->willReturn($this->createStub(AuthRequestState::class));

		$endpoint = new AuthCallback(
			bus: $this->createStub(MessageBus::class),
			connectors: $conReg,
			authRepo: $reqRep
		);

		$endpoint->run(
			params: [
				'provider' => 'smolblog',
				'state' => 'acb72fd6-3e86-400f-8c2b-8b241fc3ad67',
			]
		);
	}

	public function testItGivesNotFoundWithInvalidState() {
		$this->expectException(NotFound::class);

		$conReg = $this->createStub(ConnectorRegistrar::class);
		$conReg->method('has')->willReturn(true);

		$reqRep = $this->createStub(AuthRequestStateRepo::class);
		$reqRep->method('getAuthRequestState')->willReturn(null);

		$endpoint = new AuthCallback(
			bus: $this->createStub(MessageBus::class),
			connectors: $conReg,
			authRepo: $reqRep
		);

		$endpoint->run(
			params: [
				'provider' => 'smolblog',
				'state' => 'acb72fd6-3e86-400f-8c2b-8b241fc3ad67',
				'code' => '468e74b1-65fe-4000-8c17-4ba1832dbcdf',
			]
		);
	}
}
