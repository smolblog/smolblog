<?php

namespace Smolblog\Core;

use Composer\InstalledVersions;

/**
 * Represents a Composer package that can be loaded into the system.
 */
class PluginPackage {
	/**
	 * Create a PluginPackage from the given composer.json string.
	 *
	 * @param string $packageName Composer package name to load.
	 * @return ?PluginPackage
	 */
	public static function createFromComposer(string $packageName): ?PluginPackage {
		$packagePath = realpath(InstalledVersions::getInstallPath($packageName));
		if ($packagePath === false) {
			return null;
		}
		$composerJsonPath = $packagePath . DIRECTORY_SEPARATOR . 'composer.json';
		$composerJson = file_get_contents($composerJsonPath);
		$composer = json_decode($composerJson);
		$errors = [];

		if (!isset($composer->name)) {
			$errors[] = 'Package name is required.';
		}
		if (!isset($composer->description)) {
			$errors[] = 'Package description is required.';
		}
		if (!is_array($composer->authors) || empty($composer->authors)) {
			$errors[] = 'At least one package author is required.';
		}
		if (!isset($composer->extra?->smolblog?->title)) {
			$errors[] = 'Plugin title is required. (Set "extra: smolblog: title:")';
		}

		$pluginClass = '';
		$givenClass = $composer->extra?->smolblog?->pluginClass ?? '';
		if (empty($givenClass)) {
			$errors[] = 'Plugin pluginClass is required. (Set "extra: smolblog: pluginClass:")';
		} elseif (!class_exists($givenClass)) {
			$errors[] = "Given pluginClass `$givenClass` is not available. Make sure 'autoloader' is correctly set.";
		} elseif (!in_array(Plugin::class, class_implements($givenClass))) {
			$errors[] = "Class `$givenClass` does not implement Plugin.";
		} else {
			$pluginClass = $givenClass;
		}

		return new PluginPackage(
			package: $composer->name ?? '',
			version: InstalledVersions::getPrettyVersion($composer->name) ?? '',
			description: $composer->description ?? '',
			authors: $composer->authors ?? [],
			title: $composer->extra?->smolblog?->title ?? '',
			errors: $errors,
			pluginClass: $pluginClass,
		);
	}

	/**
	 * Create the PluginPackage object.
	 *
	 * @param string $package     Composer package name.
	 * @param string $version     Currently installed version.
	 * @param string $title       Friendly name for the plugin.
	 * @param string $description Brief description for the plugin.
	 * @param array  $authors     Authors of the plugin; see composer.json for format.
	 * @param array  $errors      Any errors found in creating the plugin; must be empty to be active.
	 * @param string $pluginClass Plugin class that loads the plugin.
	 */
	public function __construct(
		public readonly string $package,
		public readonly string $version,
		public readonly string $title,
		public readonly string $description,
		public readonly array $authors,
		public readonly array $errors,
		protected string $pluginClass,
	) {
	}

	/**
	 * Create the Plugin class given in composer.json
	 *
	 * @param App $app Smolblog App instance in use.
	 * @return ?Plugin created class
	 */
	public function createPlugin(App $app): ?Plugin {
		if ($this->pluginClass) {
			return new $this->pluginClass(smolblog: $app);
		}
		return null;
	}
}
