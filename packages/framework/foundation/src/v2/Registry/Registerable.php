<?php

namespace Smolblog\Foundation\v2\Registry;

/**
 * A service that can be registered in a registry.
 *
 * This is the basic version. If a broader configuration is required, use ConfiguredRegisterable.
 */
interface Registerable {
	/**
	 * Get the key for this service so it can be registered.
	 *
	 * @return string
	 */
	public static function getKey(): string;
}
