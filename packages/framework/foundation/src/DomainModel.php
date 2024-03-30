<?php

namespace Smolblog\Foundation;

/**
 * Class to centralize services (with dependencies) for a domain.
 */
abstract class DomainModel {
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
