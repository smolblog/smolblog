<?php

namespace Smolblog\Framework\Objects;

/**
 * Class to centralize services (with dependencies) for a domain.
 *
 * @deprecated Migrate to Smolblog\Foundation classes
 */
class DomainModel {
	public const SERVICES = [];

	/**
	 * Get services defined in this model and the dependencies they need.
	 *
	 * @return array
	 */
	public static function getDependencyMap(): array {
		return static::SERVICES;
	}
}
