<?php

namespace Smolblog\Core\Importer;

/**
 * Configuration for an Importer
 */
class ImporterConfig {
	/**
	 * Construct the configuration
	 *
	 * @param string $slug Identifier for the Importer.
	 */
	public function __construct(
		public readonly string $slug
	) {
	}
}
