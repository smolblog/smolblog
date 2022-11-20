<?php

namespace Smolblog\Core\Connector\Services;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\ConnectorRegistrar;
use Smolblog\Core\Connector\ConnectorWithRefresh;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Entities\ConnectionWriter;

final class RefreshConnectionTokenTest extends TestCase {
	public function testItReturnsTheConnectionIfTheConnectionDoesNotRefresh() {
		$connector = $this->createStub(Connector::class);

		$repo = $this->createStub(ConnectorRegistrar::class);
		$repo->method('get')->willReturn($connector);

		$service = new RefreshConnectionToken(
			connectorRepo: $repo,
			connectionWriter: $this->createStub(ConnectionWriter::class),
		);

		$connection = new Connection(
			userId: 5,
			provider: 'test',
			providerKey: '123',
			displayName: '@me',
			details: [],
		);

		$this->assertEquals($connection, $service->run(connection: $connection));
	}

	public function testItReturnsTheConnectionIfTheConnectionDoesNotNeedARefresh() {
		$connector = $this->createStub(ConnectorWithRefresh::class);
		$connector->method('connectionNeedsRefresh')->willReturn(false);

		$repo = $this->createStub(ConnectorRegistrar::class);
		$repo->method('get')->willReturn($connector);

		$service = new RefreshConnectionToken(
			connectorRepo: $repo,
			connectionWriter: $this->createStub(ConnectionWriter::class),
		);

		$connection = new Connection(
			userId: 5,
			provider: 'test',
			providerKey: '123',
			displayName: '@me',
			details: [],
		);

		$this->assertEquals($connection, $service->run(connection: $connection));
	}

	public function testItRefreshesAndSavesTheConnectionIfNecessary() {
		$refreshedConnection = new Connection(
			userId: 5,
			provider: 'test',
			providerKey: '123',
			displayName: '@me',
			details: ['access' => '456'],
		);

		$connector = $this->createStub(ConnectorWithRefresh::class);
		$connector->method('connectionNeedsRefresh')->willReturn(true);
		$connector->method('refreshConnection')->willReturn($refreshedConnection);

		$repo = $this->createStub(ConnectorRegistrar::class);
		$repo->method('get')->willReturn($connector);

		$writer = $this->createMock(ConnectionWriter::class);
		$writer->expects($this->once())->method('save')->with($this->equalTo($refreshedConnection));

		$service = new RefreshConnectionToken(
			connectorRepo: $repo,
			connectionWriter: $writer,
		);

		$origConnection = new Connection(
			userId: 5,
			provider: 'test',
			providerKey: '123',
			displayName: '@me',
			details: ['access' => '123'],
		);

		$response = $service->run(connection: $origConnection);
		$this->assertEquals($refreshedConnection, $response);
		$this->assertEquals($refreshedConnection->details['access'], $response->details['access']);
	}
}
