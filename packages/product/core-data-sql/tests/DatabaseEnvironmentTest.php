<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Schema\Schema;
use Exception;
use Smolblog\CoreDataSql\Test\DataTestBase;
use Smolblog\CoreDataSql\Test\TestDatabaseManager;

require_once __DIR__ . '/_base.php';

final class DatabaseEnvironmentTest extends DataTestBase {
	public function testItWillFailIfNoOptionsOrDsnIsProvided() {
		$this->expectException(Exception::class);

		new DatabaseEnvironment();
	}

	public function testItAcceptsADsnString() {
		$service = new DatabaseEnvironment(dsn: 'sqlite:///:memory:');

		$this->assertInstanceOf(DatabaseEnvironment::class, $service);
	}

	public function testItAcceptsATablePrefix() {
		$service = new DatabaseEnvironment(dsn: 'sqlite:///:memory:', tablePrefix: 'smol_');

		$this->assertEquals('smol_blog', $service->tableName('blog'));
	}

	public function testItWillNotPrefixATableByDefault() {
		$service = new DatabaseEnvironment(dsn: 'sqlite:///:memory:');

		$this->assertEquals('blog', $service->tableName('blog'));
	}
}
