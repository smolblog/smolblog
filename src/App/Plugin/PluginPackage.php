<?php

namespace Smolblog\App\Plugin;

/**
 * Represents a Composer package that can be loaded into the system.
 */
class PluginPackage {
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
		public readonly string $package,
		public readonly string $version,
		public readonly string $title,
		public readonly string $description,
		public readonly array $authors = [],
		public readonly array $websites = [],
	) {
	}
}
