<?php

namespace Smolblog\Core\Registrar;

interface Registerable {
	/**
	 * Get the configuration for the object. This will let the Registrar
	 * get the information it needs without having to instantiate.
	 *
	 * @return mixed
	 */
	public static function config(): mixed;
}
