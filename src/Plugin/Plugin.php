<?php

namespace Smolblog\Core\Plugin;

use Smolblog\Core\App;
use Smolblog\Core\Registrar\Registerable;

interface Plugin extends Registerable {
	/**
	 * Get the information about this Plugin
	 *
	 * @return PluginPackage
	 */
	public static function config(): PluginPackage;

	/**
	 * Plugin bootstrapping function called by the App
	 *
	 * @param App $app Smolblog App instance being intiialized.
	 * @return void
	 */
	public static function setup(App $app);
}
