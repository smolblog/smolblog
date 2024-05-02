<?php

namespace Smolblog\Foundation\Service\Registry;

use Smolblog\Foundation\Service;

/**
 * A class that registers services for a specific purpose.
 *
 * A Registry takes classes that implement a given interface. It should take a list of classes as a "configuration"
 * for the configure() method. This will allow the Registry to be lazily instantiated by the DI container.
 *
 * If a Registry does not require this, it probably shouldn't implement this interface.
 */
interface Registry extends Service {
	/**
	 * Get the interface this Registry tracks.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string;

	/**
	 * Accept the configuration for the registry.
	 *
	 * @param string[] $configuration Array of classes to register.
	 * @return void
	 */
	public function configure(array $configuration): void;
}
