<?php

namespace Smolblog\CoreDataSql\Test;

use Smolblog\CoreDataSql\DatabaseManager;
use Smolblog\Test\AppTest;

abstract class DataTestBase extends AppTest {
	const INCLUDED_MODELS = [
		\Smolblog\Core\Model::class,
		\Smolblog\CoreDataSql\Model::class,
	];

	protected function createMockServices(): array {
		return [
			DatabaseManager::class => ['props' => fn() => ['driver' => 'pdo_sqlite', 'memory' => true]],
			...parent::createMockServices(),
		];
	}
}
