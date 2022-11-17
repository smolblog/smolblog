<?php

namespace Smolblog\App\Hooks;

use Smolblog\App\Smolblog;

/**
 * Fired after the Smolblog core system has finished registering its classes and performing startup tasks.
 */
class Startup {
	/**
	 * Create the Startup event. Requires the App instance that has started up.
	 *
	 * @param Smolblog $app App instance that has finished starting up.
	 */
	public function __construct(public readonly Smolblog $app) {
	}
}
