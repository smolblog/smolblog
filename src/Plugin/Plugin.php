<?php

namespace Smolblog\Core;

interface Plugin {
	/**
	 * Create the plugin.
	 *
	 * @param App $smolblog Current Smolblog instance.
	 */
	public function __construct(App $smolblog);
}
