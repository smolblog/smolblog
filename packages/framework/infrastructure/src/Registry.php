<?php

namespace Smolblog\Framework\Infrastructure;

/**
 * A class that registers services for a specific purpose.
 *
 * A Registry takes classes that implement a given interface. While the Registry can accept classes after the fact,
 * it should take an array of these classes at construction-time as the 'configuration' parameter. This will allow
 * the Registry to be lazily instantiated by the DI container.
 *
 * If a Registry does not require this, it probably shouldn't implement this interface.
 */
interface Registry {
	/**
	 * Get the interface this Registry tracks.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string;
}
