<?php

namespace Smolblog\Core\Connection\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Events\ConnectionRefreshed;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\ConnectionTestBase;

class RefreshConnectionTest extends ConnectionTestBase {
	public function testHappyPath() {
		$userId = Identifier::fromString('8de40399-240e-4e04-bfc5-a7a4bfeffdd5');
		$connection = new Connection(
			userId: $userId,
			handler: 'testmock',
			handlerKey: 'abc1233445',
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
			handler: 'testmock',
			handlerKey: 'abc1233445',
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
