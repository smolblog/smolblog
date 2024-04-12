<?php

namespace Smolblog\Foundation\Value\Traits;

/**
 * Default implementation of a ServiceConfiguration.
 *
 * Consuming class MUST set $this->key in the constructor.
 */
trait ServiceConfigurationKit {
	/**
	 * String key that uniquely identifies the Service.
	 *
	 * @var string
	 */
	public readonly string $key;

	/**
	 * Get the service's key from this configuration.
	 *
	 * @return string
	 */
	public function getKey(): string {
		return $this->key;
	}
}
