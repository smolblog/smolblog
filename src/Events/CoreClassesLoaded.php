<?php

namespace Smolblog\Core\Events;

use Smolblog\Core\Container;

/**
 * Fired after the Smolblog core system has registered its classes into the given DI container.
 */
class CoreClassesLoaded {
	/**
	 * Create the CoreClassesLoaded event with the DI container.
	 *
	 * @param Container $container Container instance with core classes.
	 */
	public function __construct(public readonly Container $container) {
	}
}
