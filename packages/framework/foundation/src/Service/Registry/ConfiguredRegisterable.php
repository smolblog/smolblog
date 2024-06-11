<?php

namespace Smolblog\Foundation\Service\Registry;

use Smolblog\Foundation\Service;
use Smolblog\Foundation\Value\Traits\ServiceConfiguration;

/**
 * A service that can be registered in a registry using a ServiceConfiguration.
 */
interface ConfiguredRegisterable extends Service {
	/**
	 * Get the configuration for this service so it can be registered.
	 *
	 * @return ServiceConfiguration
	 */
	public static function getConfiguration(): ServiceConfiguration;
}
