<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Connection;
use Smolblog\Foundation\DomainModel;

/**
 * Set up the services and listeners for the Core Data domain model.
 */
class Model extends DomainModel {
	public const SERVICES = [
		ContentProjection::class => [
			'db' => Connection::class,
		],
		DatabaseManager::class => [],
	];

	/**
	 * Get the dependency map for this Model.
	 *
	 * @return array
	 */
	public static function getDependencyMap(): array {
		return [
			...self::SERVICES,
			Connection::class => fn($c) => $c->get(DatabaseManager::class)->getConnection(),
		];
	}
}
