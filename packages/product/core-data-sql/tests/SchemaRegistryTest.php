<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Schema\Schema;
use Smolblog\CoreDataSql\Test\DataTestBase;
use Smolblog\CoreDataSql\Test\TestSchemaRegistry;

require_once __DIR__ . '/_base.php';

final class SchemaRegistryTest extends DataTestBase {
	public function testSchemaVersionWillBeNullIfRowIsNotPresent() {
		$service = $this->app->container->get(TestSchemaRegistry::class);

		$env = $this->app->container->get(DatabaseEnvironment::class);
		$env->getConnection()->delete($env->tableName('db_manager'));

		$this->assertNull($service->testGetSchemaVersion());
	}

	public function testSchemaVersionWillUpdateIfRowIsPresent() {
		$service = $this->app->container->get(TestSchemaRegistry::class);

		$service->testSetSchemaVersion('testversion');
		$this->assertEquals('testversion', $service->testGetSchemaVersion());
	}

	public function testSchemaWillMigrate() {
		$service = $this->app->container->get(TestSchemaRegistry::class);
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

		$createSql = <<<TEST
		CREATE TABLE mc_hammer (
			id INTEGER PRIMARY KEY,
			other_mc TEXT NOT NULL
		);
		INSERT INTO mc_hammer (id, other_mc) VALUES (1, 'tobyMac');
		INSERT INTO mc_hammer (id, other_mc) VALUES (2, 'madcrasher');
		TEST;
		$db->executeStatement($createSql);

		$testConfig = [
			ContentProjection::class,
			EventStream::class,
		];

		$this->assertNotNull($service->testGetSchemaVersion());

		$firstProjection = new class implements DatabaseTableHandler {
			public static function addTableToSchema(Schema $schema, callable $tableName): Schema {
				$table = $schema->createTable($tableName('test_projection'));
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
		$db->insert($env->tableName('test_projection'), ['first_uuid' => '0d1d4377-8003-4b15-a189-028cb93013df']);

		$secondProjection = new class implements DatabaseTableHandler {
			public static function addTableToSchema(Schema $schema, callable $tableName): Schema {
				$table = $schema->createTable($tableName('test_projection'));
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
		$db->insert($env->tableName('test_projection'), [
			'first_uuid' => '0da5eef8-573b-4437-851f-08db2c98dd74',
			'first_uuid' => 'ce86d81f-0dc7-4721-a3aa-9b88562e662f',
		]);

		$this->assertEquals(
			'0d1d4377-8003-4b15-a189-028cb93013df',
			$db->fetchOne('SELECT first_uuid FROM '.$env->tableName('test_projection').' WHERE second_uuid IS NULL'),
		);

		$this->assertEquals(
			'madcrasher',
			$db->fetchOne('SELECT other_mc FROM mc_hammer WHERE id = 2'),
			'Migrations should not touch non-prefixed tables.'
		);
	}
}
