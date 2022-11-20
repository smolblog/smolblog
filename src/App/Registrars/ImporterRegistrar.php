<?php

namespace Smolblog\App\Registrars;

use Smolblog\Core\Importer\Importer;
use Smolblog\Core\Importer\ImporterRegistrar as DomainImporterRegistrar;

/**
 * Class to handle storing Importers for use later.
 */
class ImporterRegistrar extends GenericRegistrar implements DomainImporterRegistrar {
	/**
	 * Fully-qualified name for the interface to check against.
	 *
	 * @var string
	 */
	protected string $interface = Importer::class;

	/**
	 * Get an Importer indicated by the given key.
	 *
	 * @param string $key Key for class to instantiate and get.
	 * @return Importer Importer instance requested.
	 */
	public function get(string $key): Importer {
		return parent::get($key);
	}
}
