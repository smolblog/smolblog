<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\Module;
use Cavatappi\Foundation\Module\FileDiscoveryKit;
use Cavatappi\Foundation\Module\ModuleKit;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Data\ContentStateManager;
use Smolblog\Core\Media\Data\MediaRepo;

/**
 * Set up the services and listeners for the Core Data domain model.
 */
class Model implements Module {
	use FileDiscoveryKit;
	use ModuleKit;

	private static function serviceMapOverrides(): array {
		return [
			ChannelRepo::class => ChannelProjection::class,
			ConnectionRepo::class => ConnectionProjection::class,
			ContentRepo::class => ContentProjection::class,
			ContentStateManager::class => ContentProjection::class,
			MediaRepo::class => MediaProjection::class,
		];
	}
}
