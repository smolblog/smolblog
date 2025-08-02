<?php

namespace Smolblog\IntegrationTest\ReferenceApp;

use Smolblog\Core\Model as CoreModel;
use Smolblog\CoreDataSql\DatabaseEnvironment;
use Smolblog\CoreDataSql\Model as CoreDataSqlModel;
use Smolblog\Foundation\Service\KeypairGenerator;
use Smolblog\Infrastructure\AppKit;
use Smolblog\Infrastructure\Model as InfrastructureModel;
use Smolblog\Infrastructure\Registries\ServiceRegistry;

final class App {
	use AppKit;

	public readonly ServiceRegistry $container;

	public function __construct() {
		// Via https://stackoverflow.com/a/13212994/1284374.
		$randomPrefix = substr(
			str_shuffle(
				str_repeat(
					$x = 'abcdefghijklmnopqrstuvwxyz',
					ceil(8 / strlen($x))
				)
			),
			1,
			8
		);

		$dependencyMap = $this->buildDependencyMap([
			CoreModel::class,
			CoreDataSqlModel::class,
			InfrastructureModel::class,
			Model::class,
		]);

		$dependencyMap[DatabaseEnvironment::class] = [
			'props' => fn() => ['driver' => 'pdo_sqlite', 'memory' => true],
			'tablePrefix' => fn() => "smolblog_{$randomPrefix}_",
		];

		$this->container = new ServiceRegistry(
			configuration: $dependencyMap,
			supplements: $this->buildSupplementsForRegistries(array_keys($dependencyMap)),
		);
	}
}
