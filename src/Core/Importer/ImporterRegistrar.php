<?php

namespace Smolblog\Core\Importer;

use Smolblog\Framework\Registrar;

/**
 * Class to handle storing Importers for use later.
 */
interface ImporterRegistrar extends Registrar {
	/**
	 * Get an Importer indicated by the given key.
	 *
	 * @param string $key Key for class to instantiate and get.
	 * @return Importer Importer instance requested.
	 */
	public function get(string $key): Importer;
}
