<?php

namespace Smolblog\Framework;

/**
 * Interface for a class to store objects of a particular class.
 *
 * There will be points where a service will need a particular type of dependency, but the actual dependency will not
 * be known until runtime. For example, there may be a need to interact with an external website, but the particular
 * website depends on the Entity being examined.
 *
 * The Registrar holds objects of a particular type keyed to strings. These strings can be anything, including fully
 * qualified class names, but it does not matter for the most part as long as they are consistently used.
 *
 * Named consistently with the PSR-11 standard for dependency injection containers, but as this does not throw
 * exceptions it is not strictly compatible.
 */
interface Registrar {
	/**
	 * Check if this Registrar has a class registered to the given key.
	 *
	 * @param string $key Key for class to check for.
	 * @return boolean false if $this->get will return null.
	 */
	public function has(string $key): bool;

	/**
	 * Get an instance of the class indicated by the given key.
	 *
	 * @param string $key Key for class to instantiate and get.
	 * @return mixed Instance of the requested class.
	 */
	public function get(string $key): mixed;
}
