<?php

namespace Smolblog\Foundation\Service\Registry;

use Smolblog\Foundation\Service;

/**
 * A service that can be registered in a registry.
 *
 * This is the basic version. If a broader configuration is required, use ConfiguredRegisterable.
 */
interface Registerable extends Service {
	/**
	 * Get the key for this service so it can be registered.
	 *
	 * @return string
	 */
	public static function getKey(): string;
}
