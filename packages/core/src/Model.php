<?php

namespace Smolblog\Core;

use Cavatappi\Foundation\Module;
use Cavatappi\Foundation\Module\FileDiscoveryKit;
use Cavatappi\Foundation\Module\ModuleKit;
use Psr\Container\ContainerInterface;

/**
 * Set up the services and listeners for the Core domain model.
 */
class Model implements Module {
	use FileDiscoveryKit;
	use ModuleKit;

	private static function serviceMapOverrides(): array {
		return [
			// Defined here because there is an optional parameter.
			Media\Services\MediaHandlerRegistry::class => [
				'container' => ContainerInterface::class,
			],
		];
	}
}
