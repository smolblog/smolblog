<?php

namespace Smolblog\Core\Parser;

use Smolblog\Core\Registrar\Registrar;

/**
 * Class to handle storing Parsers for use later.
 */
class ParserRegistrar extends Registrar {
	/**
	 * Handle the configuration of the Parser.
	 *
	 * @param mixed $config Configuration array from the class.
	 * @return string Key to retrieve the class with.
	 */
	protected function processConfig(mixed $config): string {
		return $config->slug;
	}
}
