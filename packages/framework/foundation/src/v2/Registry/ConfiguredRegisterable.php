<?php

namespace Smolblog\Foundation\v2\Registry;

/**
 * A class that can be registered in a registry using a RegisterableConfiguration.
 */
interface ConfiguredRegisterable {
	/**
	 * Get the configuration for this class so it can be registered.
	 *
	 * @return RegisterableConfiguration
	 */
	public static function getConfiguration(): RegisterableConfiguration;
}
