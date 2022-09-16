<?php

namespace Smolblog\Core;

use Composer\InstalledVersions;

/**
 * Represents a Composer package that can be loaded into the system.
 */
class Plugin {
	/**
	 * Create a Plugin from the given composer.json string.
	 *
	 * @param string $composerJson String of contents of a valid composer.json file.
	 * @return Plugin
	 */
	public static function createFromComposer(string $composerJson): Plugin {
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
		if (!isset($composer->extra?->smolblog?->entrypoint)) {
			$errors[] = 'Plugin entrypoint is required. (Set "extra: smolblog: entrypoint:")';
		}

		$entrypoint = null;
		try {
			$entrypoint = Closure::fromCallable($composer->extra?->smolblog?->entrypoint);
		} catch (TypeError $exception) {
			$errors[] = 'Could not create entrypoint: ' . $exception->getMessage();
		}

		return new Plugin(
			package: $composer->name ?? '',
			version: InstalledVersions::getPrettyVersion($composer->name) ?? '',
			description: $composer->description ?? '',
			authors: $composer->authors ?? [],
			title: $composer->extra?->smolblog?->title ?? '',
			errors: $errors,
			active: !empty($errors),
			entrypoint: $entrypoint,
		);
	}

	/**
	 * Create the Plugin object.
	 *
	 * @param string       $package     Composer package name.
	 * @param string       $version     Currently installed version.
	 * @param string       $title       Friendly name for the plugin.
	 * @param string       $description Brief description for the plugin.
	 * @param array        $authors     Authors of the plugin; see composer.json for format.
	 * @param array        $errors      Any errors found in creating the plugin; must be empty to be active.
	 * @param boolean      $active      True if plugin is ready and available to be used.
	 * @param Closure|null $entrypoint  Closure function that loads the plugin.
	 */
	public function __construct(
		public readonly string $package,
		public readonly string $version,
		public readonly string $title,
		public readonly string $description,
		public readonly array $authors,
		public readonly array $errors,
		public readonly bool $active,
		protected ?Closure $entrypoint,
	) {
	}

	/**
	 * Provide the App instance to the Plugin so it can load its classes into the
	 * container and register for events.
	 *
	 * @param App $app Smolblog App instance in use.
	 * @return void
	 */
	public function load(App $app): void {
		$this->$entrypoint($app);
	}
}
