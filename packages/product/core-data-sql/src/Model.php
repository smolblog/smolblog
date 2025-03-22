<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Connection as DatabaseConnection;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Data\ContentStateManager;
use Smolblog\Core\Media\Data\MediaRepo;
use Smolblog\Foundation\DomainModel;

/**
 * Set up the services and listeners for the Core Data domain model.
 */
class Model extends DomainModel {
	public const SERVICES = [
		ChannelProjection::class => [
			'db' => DatabaseConnection::class,
		],
		ConnectionProjection::class => [
			'db' => DatabaseConnection::class,
		],
		ContentProjection::class => [
			'db' => DatabaseConnection::class,
		],
		DatabaseManager::class => [],
		EventStream::class => [
			'db' => DatabaseConnection::class,
		],
		MediaProjection::class => [
			'db' => DatabaseConnection::class,
		],

		ChannelRepo::class => ChannelProjection::class,
		ConnectionRepo::class => ConnectionProjection::class,
		ContentRepo::class => ContentProjection::class,
		ContentStateManager::class => ContentProjection::class,
		MediaRepo::class => MediaProjection::class,
	];

	/**
	 * Get the dependency map for this Model.
	 *
	 * @return array
	 */
	public static function getDependencyMap(): array {
		return [
			...self::SERVICES,
			DatabaseConnection::class => fn($c) => $c->get(DatabaseManager::class)->getConnection(),
		];
	}
}
