<?php

namespace Smolblog\Core\Plugin;

use Smolblog\Core\App;

interface Plugin {
	/**
	 * Create the plugin.
	 *
	 * @param App $smolblog Current Smolblog instance.
	 */
	public function __construct(App $smolblog);
}
