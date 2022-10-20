<?php

namespace Smolblog\Core\Importer;

use Smolblog\Core\Registrar\Registrar;

/**
 * Class to handle storing Importers for use later.
 */
class ImporterRegistrar extends Registrar {
	/**
	 * Handle the configuration of the Importer.
	 *
	 * @param mixed $config Configuration array from the class.
	 * @return string Key to retrieve the class with.
	 */
	protected function processConfig(mixed $config): string {
		return $config->slug;
	}

	/**
	 * Get an Importer indicated by the given key.
	 *
	 * @param string $key Key for class to instantiate and get.
	 * @return Importer Importer instance requested.
	 */
	public function get(string $key): Importer {
		return parent::get(key: $key);
	}
}
