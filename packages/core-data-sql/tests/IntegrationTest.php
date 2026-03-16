<?php

namespace Smolblog\CoreDataSql\Test;

use Smolblog\Core\Test\ApplicationStateTest;
use Smolblog\CoreDataSql\DatabaseEnvironment;

final class IntegrationTest extends ApplicationStateTest {
	public const INCLUDED_MODELS = [
		\Smolblog\Core\Model::class,
		\Smolblog\CoreDataSql\Model::class,
	];

	protected function createMockServices(): array {
		//via https://stackoverflow.com/a/13212994/1284374
		$randomPrefix = substr(
			str_shuffle(
				str_repeat(
					$x = 'abcdefghijklmnopqrstuvwxyz',
					ceil(8 / strlen($x)),
				),
			),
			1,
			8,
		);
		return [
			...parent::createMockServices(),
			DatabaseEnvironment::class => [
				'props' => fn() => ['driver' => 'pdo_sqlite', 'memory' => true],
				'tablePrefix' => fn() => $randomPrefix . '_',
			],
		];
	}
}
