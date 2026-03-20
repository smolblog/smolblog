<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\Module;
use Cavatappi\Foundation\Module\FileDiscoveryKit;
use Cavatappi\Foundation\Module\ModuleKit;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Connection\Data\AuthRequestStateRepo;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Data\ContentStateManager;
use Smolblog\Core\Media\Data\MediaRepo;
use Smolblog\Core\Site\Data\SiteRepo;
use Smolblog\Core\Site\Data\SiteUserRepo;
use Smolblog\Core\User\UserRepo;

/**
 * Set up the services and listeners for the Core Data domain model.
 */
class Model implements Module {
	use FileDiscoveryKit;
	use ModuleKit;

	private static function serviceMapOverrides(): array {
		return [
			AuthRequestStateRepo::class => ScratchPad::class,
			ChannelRepo::class => ChannelProjection::class,
			ConnectionRepo::class => ConnectionProjection::class,
			ContentRepo::class => ContentProjection::class,
			ContentStateManager::class => ContentProjection::class,
			MediaRepo::class => MediaProjection::class,
			SiteRepo::class => SiteProjection::class,
			SiteUserRepo::class => SiteProjection::class,
			UserRepo::class => UserProjection::class,
		];
	}
}
