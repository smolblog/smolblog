<?php

namespace Smolblog\App\Plugin;

use Smolblog\App\Smolblog;

interface Plugin {
	/**
	 * Get the information about this Plugin
	 *
	 * @return PluginPackage
	 */
	public static function config(): PluginPackage;

	/**
	 * Plugin bootstrapping function called by the App
	 *
	 * @param Smolblog $app Smolblog App instance being intiialized.
	 * @return void
	 */
	public static function setup(Smolblog $app);
}
