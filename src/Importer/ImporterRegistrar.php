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
}
