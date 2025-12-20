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
	public const AUTO_SERVICES = [
		ChannelProjection::class,
		ConnectionProjection::class,
		ContentProjection::class,
		DatabaseService::class,
		EventStream::class,
		MediaProjection::class,
		SchemaRegistry::class,
	];

	public const SERVICES = [
		ChannelRepo::class => ChannelProjection::class,
		ConnectionRepo::class => ConnectionProjection::class,
		ContentRepo::class => ContentProjection::class,
		ContentStateManager::class => ContentProjection::class,
		MediaRepo::class => MediaProjection::class,
	];
}
