<?php

namespace Smolblog\Foundation\v2;

/**
 * A Model that enumerates services (with dependencies) for a domain.
 */
interface WithServices {
	/**
	 * Get the Services to be registered in this Model and their dependencies.
	 *
	 * @return array<class-string, array<string, class-string|callable>|string|callable>
	 */
	public static function serviceDependencyMap(): array;
}
