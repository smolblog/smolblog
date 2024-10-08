<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Test\TestCase;
use Smolblog\Core\Connector\Commands\BeginAuthRequest;
use Smolblog\Core\Connector\Commands\FinishAuthRequest;
use Smolblog\Core\Connector\Commands\RefreshChannels;
use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\ConnectorInitData;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Events\ConnectionEstablished;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\Kits\EventComparisonTestKit;

final class AuthRequestServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesTheBeginAuthRequestCommand(): void {
		$authUrl = 'https://something.com/';

		$connector = $this->createMock(Connector::class);
		$connector->expects($this->once())
		          ->method('getInitializationData')
							->willReturn(new ConnectorInitData(url: $authUrl, state: 'bob', info: []));

		$connectors = $this->createMock(ConnectorRegistry::class);
		$connectors->expects($this->once())
							 ->method('get')
							 ->willReturn($connector);

		$stateRepo = $this->createMock(AuthRequestStateRepo::class);
		$stateRepo->expects($this->once())->method('saveAuthRequestState');

		$messageBus = $this->createStub(MessageBus::class);

		$service = new AuthRequestService(
			connectors: $connectors,
			stateRepo: $stateRepo,
			messageBus: $messageBus,
		);

		$command = new BeginAuthRequest(userId: $this->randomId(), provider: 'smol', callbackUrl: '//smol.blog');
		$service->onBeginAuthRequest($command);

		$this->assertEquals($authUrl, $command->redirectUrl);
	}

	public function testItHandlesTheFinishAuthRequestCommand(): void {
		$userId = $this->randomId();
		$returnedConnection = new Connection(
			userId: $userId,
			provider: 'something',
			providerKey: 'something',
			displayName: 'something',
			details: ['something'=>'else'],
		);

		$expectedEvent = new ConnectionEstablished(
			provider: 'something',
			providerKey: 'something',
			displayName: 'something',
			details: ['something' => 'else'],
			connectionId: $returnedConnection->id,
			userId: $userId,
		);

		$connector = $this->createMock(Connector::class);
		$connector->expects($this->once())->method('createConnection')->willReturn($returnedConnection);

		$connectors = $this->createStub(ConnectorRegistry::class);
		$connectors->method('get')->willReturn($connector);

		$stateRepo = $this->createStub(AuthRequestStateRepo::class);
		$stateRepo->method('getAuthRequestState')->willReturn(new AuthRequestState(
			key: 'two',
			userId: $userId,
			provider: 'smol',
			info: ['six' => 'eight'],
			returnToUrl: 'https://smol.blog/callback',
		));

		$messageBus = $this->createMock(MessageBus::class);
		$messageBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$service = new AuthRequestService(
			connectors: $connectors,
			stateRepo: $stateRepo,
			messageBus: $messageBus,
		);

		$command = new FinishAuthRequest(
			provider: 'smol',
			stateKey: 'two',
			code: 'abc123',
		);

		$service->onFinishAuthRequest($command);

		$this->assertEquals('https://smol.blog/callback', $command->returnToUrl);
	}
}
