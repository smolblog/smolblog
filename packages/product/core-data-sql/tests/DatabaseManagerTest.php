<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Schema\Schema;
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

	public function testSchemaWillMigrate() {
		$service = $this->app->container->get(TestDatabaseManager::class);
		$db = $service->getConnection();
		$testConfig = [
			ContentProjection::class,
			EventStream::class,
		];

		$this->assertNotNull($service->testGetSchemaVersion());

		$firstProjection = new class implements DatabaseTableHandler {
			public static function addTableToSchema(Schema $schema): Schema {
				$table = $schema->createTable('test_projection');
				$table->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
				$table->addColumn('first_uuid', 'guid');
				$table->setPrimaryKey(['dbid']);
				return $schema;
			}
		};
		$service->configure([
			...$testConfig,
			get_class($firstProjection),
		]);
		$db->insert('test_projection', ['first_uuid' => '0d1d4377-8003-4b15-a189-028cb93013df']);

		$secondProjection = new class implements DatabaseTableHandler {
			public static function addTableToSchema(Schema $schema): Schema {
				$table = $schema->createTable('test_projection');
				$table->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
				$table->addColumn('first_uuid', 'guid');
				$table->addColumn('second_uuid', 'guid', ['notnull' => false]);
				$table->setPrimaryKey(['dbid']);
				return $schema;
			}
		};
		$service->configure([
			...$testConfig,
			get_class($secondProjection),
		]);
		$db->insert('test_projection', [
			'first_uuid' => '0da5eef8-573b-4437-851f-08db2c98dd74',
			'first_uuid' => 'ce86d81f-0dc7-4721-a3aa-9b88562e662f',
		]);

		$this->assertEquals(
			'0d1d4377-8003-4b15-a189-028cb93013df',
			$db->fetchOne('SELECT first_uuid FROM test_projection WHERE second_uuid IS NULL'),
		);
	}
}
