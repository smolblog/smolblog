<?php

namespace Smolblog\CoreDataSql\Test;

use Smolblog\CoreDataSql\DatabaseManager;
use Smolblog\Test\AppTest;

final class TestDatabaseManager extends DatabaseManager {
	public function testGetSchemaVersion(): ?string { return $this->getSchemaVersion(); }
	public function testSetSchemaVersion(string $version): void { $this->setSchemaVersion($version); }
}

abstract class DataTestBase extends AppTest {
	const INCLUDED_MODELS = [
		\Smolblog\Core\Model::class,
		\Smolblog\CoreDataSql\Model::class,
	];

	protected function createMockServices(): array {
		return [
			DatabaseManager::class => TestDatabaseManager::class,
			TestDatabaseManager::class => ['props' => fn() => ['driver' => 'pdo_sqlite', 'memory' => true]],
			...parent::createMockServices(),
		];
	}
}
