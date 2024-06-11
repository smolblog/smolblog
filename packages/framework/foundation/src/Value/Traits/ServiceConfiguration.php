<?php

namespace Smolblog\Foundation\Value\Traits;

/**
 * An object that stores configuration for a ConfiguredRegisterable service.
 */
interface ServiceConfiguration {
	/**
	 * Get the service's key from this configuration.
	 *
	 * @return string
	 */
	public function getKey(): string;
}
