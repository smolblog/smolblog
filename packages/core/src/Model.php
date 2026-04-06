<?php

namespace Smolblog\Core;

use Cavatappi\Foundation\Module;
use Cavatappi\Foundation\Module\FileDiscoveryKit;
use Cavatappi\Foundation\Module\ModuleKit;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Psr\Container\ContainerInterface;

/**
 * Set up the services and listeners for the Core domain model.
 */
class Model implements Module {
	use FileDiscoveryKit;
	use ModuleKit;

	private static function serviceMapOverrides(): array {
		return [
			// Setting the default permissions service.
			Permissions\SitePermissionsService::class => Permissions\DefaultPermissionsService::class,
			Permissions\GlobalPermissionsService::class => Permissions\DefaultPermissionsService::class,
			FinfoMimeTypeDetector::class => [],
		];
	}
}
