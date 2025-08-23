<?php

namespace Smolblog\Foundation\v2\Registry;

/**
 * A class that registers classes for a specific purpose.
 *
 * A Registry takes classes that implement a given interface. It should take a list of classes as a "configuration"
 * for the configure() method. This will allow the Registry to be lazily instantiated by the DI container.
 *
 * Building a Registry this way allows the configuration to be auto-generated.
 *
 * @template I
 */
interface Registry {
	/**
	 * Get the interface this Registry tracks.
	 *
	 * @return class-string<I>
	 */
	public static function getInterfaceToRegister(): string;

	/**
	 * Accept the configuration for the registry.
	 *
	 * @param class-string<I>[] $configuration Array of classes to register.
	 * @return void
	 */
	public function configure(array $configuration): void;
}
