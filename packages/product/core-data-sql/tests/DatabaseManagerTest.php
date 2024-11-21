<?php

namespace Smolblog\CoreDataSql;

use Exception;
use Smolblog\CoreDataSql\Test\DataTestBase;
use Smolblog\CoreDataSql\Test\TestDatabaseManager;

require_once __DIR__ . '/_base.php';

final class DatabaseManagerTest extends DataTestBase {
	public function testItWillFailIfNoOptionsOrDsnIsProvided() {
		$this->expectException(Exception::class);

		new DatabaseManager();
	}

	public function testItAcceptsADsnString() {
		$service = new DatabaseManager(dsn: 'sqlite:///:memory:');

		$this->assertInstanceOf(DatabaseManager::class, $service);
	}

	public function testSchemaVersionWillBeNullIfRowIsNotPresent() {
		$service = $this->app->container->get(TestDatabaseManager::class);

		$db = $service->getConnection();
		$db->delete('db_manager');

		$this->assertNull($service->testGetSchemaVersion());
	}

	public function testSchemaVersionWillUpdateIfRowIsPresent() {
		$service = $this->app->container->get(TestDatabaseManager::class);

		$service->testSetSchemaVersion('testversion');
		$this->assertEquals('testversion', $service->testGetSchemaVersion());
	}
}
