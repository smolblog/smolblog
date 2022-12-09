<?php

namespace Smolblog\App\Plugin;

use Smolblog\Framework\Value;

/**
 * Represents a Composer package that can be loaded into the system.
 */
readonly class PluginPackage extends Value {
	/**
	 * Create the PluginPackage object.
	 *
	 * @param string $package     Composer package name.
	 * @param string $version     Currently installed version.
	 * @param string $title       Friendly name for the plugin.
	 * @param string $description Brief description for the plugin.
	 * @param array  $authors     Authors of the plugin; see composer.json for format.
	 * @param array  $websites    Websites for the plugin in 'name' => 'url' format.
	 */
	public function __construct(
		public string $package,
		public string $version,
		public string $title,
		public string $description,
		public array $authors = [],
		public array $websites = [],
	) {
	}
}
