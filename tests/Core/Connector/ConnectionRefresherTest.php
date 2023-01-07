<?php

namespace Smolblog\Core\Connector\Services;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Entities\ConnectionWriter;
use Smolblog\Core\Connector\Events\ConnectionRefreshed;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

final class ConnectionRefresherTest extends TestCase {
	use EventComparisonTestKit;

	private Connector $connector;
	private Connection $connection;
	private Connection $refreshedConnection;
	private MessageBus $messageBus;
	private ConnectionRefresher $service;

	public function setUp(): void {
		$this->connector = $this->createStub(Connector::class);

		$repo = $this->createStub(ConnectorRegistrar::class);
		$repo->method('get')->willReturn($this->connector);

		$this->connection = new Connection(
			userId: Identifier::createRandom(),
			provider: 'test',
			providerKey: '123',
			displayName: '@me',
			details: ['access' => '123'],
		);

		$this->refreshedConnection = new Connection(
			userId: $this->connection->userId,
			provider: $this->connection->provider,
			providerKey: $this->connection->providerKey,
			displayName: $this->connection->displayName,
			details: ['access' => '456'],
		);

		$this->messageBus = $this->createMock(MessageBus::class);

		$this->service = new ConnectionRefresher(
			connectorRepo: $repo,
			messageBus: $this->messageBus,
		);
	}

	private function setUpForRefresh(): void {
		$this->connector->method('connectionNeedsRefresh')->willReturn(true);
		$this->connector->method('refreshConnection')->willReturn($this->refreshedConnection);

		$this->messageBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo(
			new ConnectionRefreshed(
				details: ['access' => '456'],
				connectionId: $this->refreshedConnection->id,
				// TODO: replace with system account ID.
				userId: Identifier::fromString('e3f38a3e-eb0f-48f2-8803-6892a87ed20c'),
			)
		));
	}

	public function testItReturnsTheConnectionIfTheConnectionDoesNotNeedARefresh() {
		$this->connector->method('connectionNeedsRefresh')->willReturn(false);

		$this->assertEquals($this->connection, $this->service->refresh(connection: $this->connection));
	}

	public function testItRefreshesAndSavesTheConnectionIfNecessary() {
		$this->setUpForRefresh();

		$response = $this->service->refresh(connection: $this->connection);
		$this->assertEquals($this->refreshedConnection, $response);
	}

	public function testItDoesNotChangeTheConnectionByIdQueryWhenARefreshIsNotNeeded() {
		$this->connector->method('connectionNeedsRefresh')->willReturn(false);

		$query = new ConnectionById(connectionId: $this->connection->id);
		$query->results = $this->connection;

		$this->service->checkOnConnectionById($query);
		$this->assertEquals($this->connection, $query->results);
	}

	public function testItUpdatesTheConnectionByIdQueryWhenARefreshIsNeeded() {
		$this->setUpForRefresh();

		$query = new ConnectionById(connectionId: $this->connection->id);
		$query->results = $this->connection;

		$this->service->checkOnConnectionById($query);
		$this->assertEquals($this->refreshedConnection, $query->results);
	}
}
