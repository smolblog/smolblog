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
use Smolblog\Core\Connection\Events\ConnectionEstablished;
use Smolblog\Core\Connection\Services\ConnectionHandler;
use Smolblog\Core\Connection\Services\ConnectionHandlerRegistry;
use Smolblog\Core\Model;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\MockBases\ConnectionHandlerTestBase;
use Smolblog\Test\ModelTest;

class RefreshChannelsTest extends ModelTest {
	const INCLUDED_MODELS = [Model::class];

	private ConnectionRepo & Stub $connections;
	private ConnectionHandler & Stub $handler;

	protected function createMockServices(): array {
		$this->connections = $this->createStub(ConnectionRepo::class);
		$this->handler = $this->createStub(ConnectionHandlerTestBase::class);

		return [
			ConnectionRepo::class => fn() => $this->connections,
			ConnectionHandlerTestBase::class => fn() => $this->handler,
		];
	}

	public function testHappyPathWithCommand() {
		$userId = Identifier::fromString('8de40399-240e-4e04-bfc5-a7a4bfeffdd5');
		$connection = new Connection(
			userId: $userId,
			provider: 'testmock',
			providerKey: 'abc1233445',
			displayName: 'Test Account',
			details: ['smol' => 'blog'],
		);

		$command = new RefreshChannels(userId: $userId, connectionId: $connection->getId());

		$this->connections->method('connectionById')->willReturn($connection);

		$this->expectSuccessfulRefresh(userId: $userId, connection: $connection);

		$this->app->execute($command);
	}

	public function testHappyPathWithEvent() {
		$userId = Identifier::fromString('8de40399-240e-4e04-bfc5-a7a4bfeffdd5');
		$connection = new Connection(
			userId: $userId,
			provider: 'testmock',
			providerKey: 'abc1233445',
			displayName: 'Test Account',
			details: ['smol' => 'blog'],
		);

		$event = new ConnectionEstablished(
			provider: 'testmock',
			providerKey: $connection->providerKey,
			displayName: $connection->displayName,
			details: $connection->details,
			userId: $userId,
		);

		$this->expectSuccessfulRefresh(userId: $userId, connection: $connection);

		$this->app->dispatch($event);
	}

	public function testInvalidId() {
		$this->expectException(EntityNotFound::class);
		$this->connections->method('connectionById')->willReturn(null);

		$this->app->execute(new RefreshChannels(userId: $this->randomId(), connectionId: $this->randomId()));
	}

	private function expectSuccessfulRefresh(Connection $connection, Identifier $userId) {
		// do nothing yet.
	}
}
