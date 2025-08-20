<?php

namespace Smolblog\Foundation\v2\Value\Traits;

/**
 * An object that stores configuration for a ConfiguredRegisterable service.
 */
interface ServiceConfiguration {
	/**
	 * Get the service's key from this configuration.
	 *
	 * @var string
	 */
	public string $key { get; }
}
