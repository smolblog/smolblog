<?php

namespace Smolblog\Core\Registrar;

interface Registerable {
	/**
	 * Store the configuration for the object as a static array. This will let the Registrar
	 * get the information it needs without having to instantiate.
	 */
	public const CONFIG = [];
}
