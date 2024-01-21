<?php

namespace Smolblog\Test\Kits;

use Closure;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

trait DatabaseTestKit {
	private Connection $db;

	private function initDatabaseWithTable(string $name, Closure $builder): void {
		$manager = new Manager();
		$manager->addConnection([
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => '',
		]);
		$manager->getConnection()->getSchemaBuilder()->create($name, $builder);

		$this->db = $manager->getConnection();
	}

	protected function assertOnlyTableEntryEquals(Builder $table, mixed ...$expected) {
		$this->assertEquals((object)$expected, $table->first());
		$this->assertEquals(1, $table->count());
	}

	protected function assertTableEmpty(Builder $table) {
		$this->assertEquals(0, $table->count(), 'Table not empty: found ' . print_r($table->first(), true));
	}
}
