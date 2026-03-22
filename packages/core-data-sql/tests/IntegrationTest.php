<?php

namespace Smolblog\CoreDataSql\Test;

use PHPUnit\Framework\Attributes\CoversNothing;
use Smolblog\Core\Test\ApplicationStateTest;
use Smolblog\CoreDataSql\DatabaseEnvironment;

#[CoversNothing]
final class IntegrationTest extends ApplicationStateTest {
	public const INCLUDED_MODELS = [
		\Smolblog\Core\Model::class,
		\Smolblog\CoreDataSql\Model::class,
	];

	private static DatabaseEnvironment $testDb;

	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();
		self::$testDb = new DatabaseEnvironment(
			props: ['driver' => 'pdo_sqlite', 'memory' => true],
			tablePrefix: 'sb_',
		);
	}

	protected function setUp(): void
	{
		parent::setUp();
	}

	protected function createMockServices(): array {
		return [
			...parent::createMockServices(),
			DatabaseEnvironment::class => fn() => self::$testDb,
		];
	}
}
