<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\Factories\UuidFactory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Events\ConnectionDeleted;
use Smolblog\Core\Connection\Events\ConnectionEstablished;
use Smolblog\Core\Connection\Events\ConnectionRefreshed;
use Smolblog\CoreDataSql\Test\DataTestBase;
use stdClass;

#[AllowMockObjectsWithoutExpectations]
final class ConnectionProjectionTest extends DataTestBase {
	public function setUpUserConnections() {
		$userId = UuidFactory::fromString('6e648ea4-de43-45ad-9c5a-12eeaf9afd41');

		$connections = [
			'sameUserOne' => new Connection(
				userId: $userId,
				handler: 'test',
				handlerKey: 'sameUserOne',
				displayName: 'Same User One',
				details: ['one' => 2],
			),
			'otherUserOne' => new Connection(
				userId: $this->randomId(),
				handler: 'test',
				handlerKey: 'otherUser',
				displayName: 'Other User',
				details: ['five' => 6],
			),
			'sameUserTwo' => new Connection(
				userId: $userId,
				handler: 'other',
				handlerKey: 'sameUserTwo',
				displayName: 'Same User Two',
				details: ['three' => 4],
			),
			'otherUserTwo' => new Connection(
				userId: $this->randomId(),
				handler: 'other',
				handlerKey: 'otherUserTwo',
				displayName: 'Other User Two',
				details: ['seven' => 8],
			),
		];

		foreach ($connections as $conn) {
			$this->app->dispatch(new ConnectionEstablished(
				handler: $conn->handler,
				handlerKey: $conn->handlerKey,
				displayName: $conn->displayName,
				details: $conn->details,
				userId: $conn->userId,
			));
		}

		return $connections;
	}

	public function testConnectionEstablished() {
		$projection = $this->app->container->get(ConnectionProjection::class);
		$connection = new Connection(
			userId: $this->randomId(),
			handler: 'test',
			handlerKey: '123456',
			displayName: 'Test Connection',
			details: ['one' => 2],
		);

		$event = new ConnectionEstablished(
			handler: 'test',
			handlerKey: '123456',
			displayName: 'Test Connection',
			details: ['one' => 2],
			userId: $connection->userId,
		);

		$this->assertNull($projection->connectionById($connection->id));

		$this->app->dispatch($event);
		$this->assertValueObjectEquals($connection, $projection->connectionById($connection->id));

		$newConnection = $connection->with(details: ['abc' => 456]);
		$this->app->dispatch(new ConnectionEstablished(
			handler: 'test',
			handlerKey: '123456',
			displayName: 'Test Connection',
			details: ['abc' => 456],
			userId: $connection->userId,
		));
		$this->assertValueObjectEquals($newConnection, $projection->connectionById($connection->id));
	}

	public function testConnectionRefreshed() {
		$projection = $this->app->container->get(ConnectionProjection::class);
		$connection = new Connection(
			userId: $this->randomId(),
			handler: 'test',
			handlerKey: '123456',
			displayName: 'Test Connection',
			details: ['one' => 2],
		);
		$this->app->dispatch(new ConnectionEstablished(
			handler: 'test',
			handlerKey: '123456',
			displayName: 'Test Connection',
			details: ['one' => 2],
			userId: $connection->userId,
		));
		$this->assertValueObjectEquals($connection, $projection->connectionById($connection->id));

		$this->app->dispatch(new ConnectionRefreshed(
			details: ['answer' => 42],
			entityId: $connection->id,
			userId: $connection->userId,
		));
		$this->assertValueObjectEquals(
			$connection->with(details: ['answer' => 42]),
			$projection->connectionById($connection->id),
		);
	}

	public function testConnectionDeleted() {
		$projection = $this->app->container->get(ConnectionProjection::class);

		$addEvent = new ConnectionEstablished(
			handler: 'test',
			handlerKey: '123456',
			displayName: 'Test Connection',
			details: ['one' => 2],
			userId: $this->randomId(),
		);
		$this->app->dispatch($addEvent);
		$this->assertValueObjectEquals(
			$addEvent->getConnectionObject(),
			$projection->connectionById($addEvent->entityId ?? UuidFactory::nil()),
		);

		$this->app->dispatch(new ConnectionDeleted(
			entityId: $addEvent->entityId ?? UuidFactory::nil(),
			userId: $addEvent->userId,
		));
		$this->assertNull($projection->connectionById($addEvent->entityId ?? UuidFactory::nil()));
	}

	public function testConnectionBelongsToUser() {
		$projection = $this->app->container->get(ConnectionProjection::class);
		$connections = $this->setUpUserConnections();
		$userId = UuidFactory::fromString('6e648ea4-de43-45ad-9c5a-12eeaf9afd41');

		$this->assertTrue($projection->connectionBelongsToUser(
			connectionId: $connections['sameUserOne']->id,
			userId: $userId,
		));
		$this->assertTrue($projection->connectionBelongsToUser(
			connectionId: $connections['sameUserTwo']->id,
			userId: $userId,
		));

		$this->assertFalse($projection->connectionBelongsToUser(
			connectionId: $connections['otherUserOne']->id,
			userId: $userId,
		));
		$this->assertFalse($projection->connectionBelongsToUser(
			connectionId: $connections['otherUserTwo']->id,
			userId: $userId,
		));
	}

	public function testConnectionsForUser() {
		$projection = $this->app->container->get(ConnectionProjection::class);
		$connections = $this->setUpUserConnections();
		$userId = UuidFactory::fromString('6e648ea4-de43-45ad-9c5a-12eeaf9afd41');

		$expected = [$connections['sameUserOne'], $connections['sameUserTwo']];
		$this->assertEquals($expected, $projection->connectionsForUser($userId));
	}

	public function testConnectionRefreshedSilentlyFailsWhenConnectionDoesNotExist() {
		$projection = $this->app->container->get(ConnectionProjection::class);
		$connectionId = $this->randomId();
		$this->assertNull($projection->connectionById($connectionId));

		$this->app->dispatch(new ConnectionRefreshed(
			details: ['one' => 1],
			entityId: $connectionId,
			userId: $this->randomId(),
		));

		$this->assertNull($projection->connectionById($connectionId));
	}
}
