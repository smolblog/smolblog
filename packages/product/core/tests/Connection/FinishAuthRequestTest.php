<?php

namespace Smolblog\Core\Connection\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Connection\Entities\AuthRequestState;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Events\ConnectionEstablished;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Exceptions\ServiceNotRegistered;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\ConnectionTestBase;

class FinishAuthRequestTest extends ConnectionTestBase {
	public function testHappyPath() {
		$userId = Identifier::fromString('8de40399-240e-4e04-bfc5-a7a4bfeffdd5');
		$command = new FinishAuthRequest(
			provider: 'testmock',
			stateKey: '0ab41adf-ef37-4b51-bee3-d38bfb1b0b7a',
			code: '2d6532ef-0def-44fa-b573-5f7ec226934d',
		);

		$connection = new Connection(
			userId: $userId,
			provider: 'testmock',
			providerKey: 'acct1234',
			displayName: 'Test Connection',
			details: ['smol', 'blog'],
		);
		$state = new AuthRequestState(
			key: '0ab41adf-ef37-4b51-bee3-d38bfb1b0b7a',
			userId: $userId,
			provider: 'testmock',
			info: ['smol' => 'blog'],
			returnToUrl: '//dashboard.smolblog.com/account/connections',
		);

		$this->stateRepo->method('getAuthRequestState')->willReturn($state);
		$this->handler->expects($this->once())->method('createConnection')->with(
			code: '2d6532ef-0def-44fa-b573-5f7ec226934d',
			info: $state,
		)->willReturn($connection);

		$event = new ConnectionEstablished(
			provider: 'testmock',
			providerKey: $connection->providerKey,
			displayName: $connection->displayName,
			details: $connection->details,
			userId: $userId,
			entityId: $connection->getId(),
		);

		$this->assertObjectEquals($connection, $event->getConnectionObject());
		$this->expectEvent($event);

		$redirectUrl = $this->app->execute($command);
		$this->assertEquals('//dashboard.smolblog.com/account/connections', $redirectUrl);
	}

	public function testNoStateFound() {
		$this->expectException(EntityNotFound::class);
		$this->stateRepo->method('getAuthRequestState')->willReturn(null);

		$this->app->execute(new FinishAuthRequest(
			provider: 'testmock',
			stateKey: '60bedf14-4d2f-4ea0-afa3-760930c1819a',
			code: '1e1dec9a-a02b-42df-8599-6322cc219bcc',
		));
	}

	public function testProviderNotRegistered() {
		$this->expectException(ServiceNotRegistered::class);

		$this->app->execute(new FinishAuthRequest(
			provider: 'not registered',
			stateKey: '60bedf14-4d2f-4ea0-afa3-760930c1819a',
			code: '1e1dec9a-a02b-42df-8599-6322cc219bcc',
		));
	}
}
