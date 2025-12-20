<?php

namespace Smolblog\CoreDataSql\Test;

use Smolblog\CoreDataSql\DatabaseEnvironment;
use Smolblog\CoreDataSql\SchemaRegistry;
use Smolblog\Test\AppTest;

final class TestSchemaRegistry extends SchemaRegistry {
	public function testGetSchemaVersion(): ?string { return $this->getSchemaVersion(); }
	public function testSetSchemaVersion(string $version): void { $this->setSchemaVersion($version); }
}

abstract class DataTestBase extends AppTest {
	const INCLUDED_MODELS = [
		\Smolblog\Core\Model::class,
		\Smolblog\CoreDataSql\Model::class,
	];

	protected function createMockServices(): array {
		//via https://stackoverflow.com/a/13212994/1284374
		$randomPrefix = substr(
			str_shuffle(
				str_repeat(
					$x='abcdefghijklmnopqrstuvwxyz',
					ceil(8/strlen($x))
				)
			),1,8);
		return [
			...parent::createMockServices(),
			SchemaRegistry::class => TestSchemaRegistry::class,
			TestSchemaRegistry::class => ['env' => DatabaseEnvironment::class],
			DatabaseEnvironment::class => [
				'props' => fn() => ['driver' => 'pdo_sqlite', 'memory' => true],
				'tablePrefix' => fn() => $randomPrefix . '_',
			],
		];
	}
}
