<?php

namespace Smolblog\CoreDataSql;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Exception;
use Smolblog\CoreDataSql\Test\DataTestBase;

#[AllowMockObjectsWithoutExpectations]
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
