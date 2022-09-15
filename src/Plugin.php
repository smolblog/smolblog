<?php

namespace Smolblog\Core;

/**
 * Standard interface for a Smolblog plugin entrypoint.
 */
interface Plugin {
	/**
	 * Provide the App instance to the Plugin so it can load its classes into the
	 * container and register for events.
	 *
	 * @param App $app Smolblog App instance in use.
	 * @return void
	 */
	public function load(App $app): void;
}
