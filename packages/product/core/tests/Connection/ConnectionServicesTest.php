<?php

namespace Smolblog\Core\Connection;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Connection\Services\ConnectionDataService;
use Smolblog\Core\Connection\Services\ConnectionHandlerRegistry;
use Smolblog\Test\ConnectionTestBase;

final class ConnectionServicesTest extends ConnectionTestBase {
	public function testConnectionHandlerRegistryCanProvideAllRegisteredHandlers() {
		$handlers = $this->app->container->get(ConnectionHandlerRegistry::class)->availableConnectionHandlers();

		$this->assertContains('testmock', $handlers);
		$this->assertContainsOnlyString($handlers);
	}

	public function testConnectionDataServiceWillQueryOwnUserConnections() {
		$this->globalPerms->method('canManageOtherConnections')->willReturn(false);
		$userId = $this->randomId();

		$this->connections->expects($this->once())->method('connectionsForUser');

		$this->app->container->get(ConnectionDataService::class)
			->connectionsForUser(connectionUserId: $userId, userId: $userId);
	}

	public function testConnectionDataServiceWillQueryOtherUserConnectionsIfPermissioned() {
		$this->globalPerms->method('canManageOtherConnections')->willReturn(true);
		$userId = $this->randomId();

		$this->connections->expects($this->once())->method('connectionsForUser');

		$this->app->container->get(ConnectionDataService::class)
			->connectionsForUser(connectionUserId: $this->randomId(), userId: $userId);
	}

	public function testConnectionDataServiceWillNotQueryOtherUserConnectionsIfNotPermissioned() {
		$this->globalPerms->method('canManageOtherConnections')->willReturn(false);
		$userId = $this->randomId();

		$this->connections->expects($this->never())->method('connectionsForUser');

		$result = $this->app->container->get(ConnectionDataService::class)
			->connectionsForUser(connectionUserId: $this->randomId(), userId: $userId);
		$this->assertEquals([], $result);
	}
}
