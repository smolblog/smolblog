<?php

namespace Smolblog\Core\Connection\Commands;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Smolblog\Core\Connection\Data\AuthRequestStateRepo;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Connection\Entities\AuthRequestState;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Entities\ConnectionInitData;
use Smolblog\Core\Connection\Events\ConnectionDeleted;
use Smolblog\Core\Connection\Events\ConnectionRefreshed;
use Smolblog\Core\Connection\Services\ConnectionHandler;
use Smolblog\Core\Model;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\MockBases\ConnectionHandlerTestBase;
use Smolblog\Test\ModelTest;

class RefreshConnectionTest extends ModelTest {
	const INCLUDED_MODELS = [Model::class];

	private ConnectionRepo & Stub $connections;
	private ConnectionHandler & MockObject $handler;

	protected function createMockServices(): array {
		$this->connections = $this->createStub(ConnectionRepo::class);
		$this->handler = $this->createMock(ConnectionHandlerTestBase::class);

		return [
			ConnectionRepo::class => fn() => $this->connections,
			ConnectionHandlerTestBase::class => fn() => $this->handler,
		];
	}

	public function testHappyPath() {
		$userId = Identifier::fromString('8de40399-240e-4e04-bfc5-a7a4bfeffdd5');
		$connection = new Connection(
			userId: $userId,
			provider: 'testmock',
			providerKey: 'abc1233445',
			displayName: 'Test Account',
			details: ['smol' => 'blog'],
		);
		$command = new RefreshConnection(userId: $userId, connectionId: $connection->getId());

		$this->connections->method('connectionById')->willReturn($connection);

		$this->handler->method('connectionNeedsRefresh')->willReturn(true);
		$this->handler->expects($this->once())
			->method('refreshConnection')
			->with(connection: $connection)
			->willReturn($connection->with(details: ['shop' => 'small']));

		$this->expectEvent(new ConnectionRefreshed(
			details: ['shop' => 'small'],
			entityId: $connection->getId(),
			userId: $userId,
		));

		$this->app->execute($command);
	}

	public function testNotFound() {
		$this->expectException(EntityNotFound::class);
		$this->connections->method('connectionById')->willReturn(null);

		$this->app->execute(new RefreshConnection(userId: $this->randomId(), connectionId: $this->randomId()));
	}

	public function testNoRefreshNeeded() {
		$userId = Identifier::fromString('8de40399-240e-4e04-bfc5-a7a4bfeffdd5');
		$connection = new Connection(
			userId: $userId,
			provider: 'testmock',
			providerKey: 'abc1233445',
			displayName: 'Test Account',
			details: ['smol' => 'blog'],
		);
		$command = new RefreshConnection(userId: $userId, connectionId: $connection->getId());

		$this->connections->method('connectionById')->willReturn($connection);

		$this->handler->method('connectionNeedsRefresh')->willReturn(false);
		$this->handler->expects($this->never())->method('refreshConnection');

		$this->expectNoEvents();

		$this->app->execute($command);
	}
}
