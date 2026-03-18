<?php

namespace Smolblog\CoreDataSql\Test;

use Smolblog\Core\Test\ApplicationStateTest;
use Smolblog\CoreDataSql\DatabaseEnvironment;

final class IntegrationTest extends ApplicationStateTest {
	public const INCLUDED_MODELS = [
		\Smolblog\Core\Model::class,
		\Smolblog\CoreDataSql\Model::class,
	];

	private static DatabaseEnvironment $testDb;

	public static function setUpBeforeClass(): void
	{
		echo 'How do I work this? ';
		parent::setUpBeforeClass();
		echo 'Is this real life? ';
		self::$testDb = new DatabaseEnvironment(
			props: ['driver' => 'pdo_sqlite', 'memory' => true],
			tablePrefix: 'sb_',
		);
		echo 'Is this gonna be forever? ';
	}

	protected function setUp(): void
	{
		echo 'Under the water, carry the water. ';
		parent::setUp();
	}

	protected function createMockServices(): array {
		echo 'Where does that highway go to? ';
		return [
			...parent::createMockServices(),
			DatabaseEnvironment::class => fn() => self::$testDb,
		];
	}

	public static function tearDownAfterClass(): void
	{
		echo 'How did I get here? ';
		unset(self::$testDb);
	}
}
