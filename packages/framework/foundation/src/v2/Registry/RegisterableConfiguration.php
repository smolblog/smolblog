<?php

namespace Smolblog\Foundation\v2\Registry;

/**
 * An object that stores configuration for a ConfiguredRegisterable service.
 */
interface RegisterableConfiguration {
	/**
	 * Get the service's key from this configuration.
	 *
	 * @var string
	 */
	public string $key { get; }
}
