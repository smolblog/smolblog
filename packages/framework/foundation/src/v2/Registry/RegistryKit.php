<?php

namespace Smolblog\Foundation\v2\Registry;

use Smolblog\Foundation\Exceptions\CodePathNotSupported;

/**
 * Useful defaults for a Registry.
 *
 * @template I
 */
trait RegistryKit {
	/**
	 * Store the class map.
	 *
	 * @var array<string, class-string<I>>
	 */
	private $library = [];

	/**
	 * Store the configurations.
	 *
	 * @var array<string, class-string<RegisterableConfiguration>>
	 */
	private $configs = [];

	/**
	 * Configure the Registry
	 *
	 * @throws CodePathNotSupported If the Registry's interface is not Registerable.
	 *
	 * @param class-string<Registerable|ConfiguredRegisterable>[] $configuration List of classes to register.
	 * @return void
	 */
	public function configure(array $configuration): void {
		$interface = self::getInterfaceToRegister();
		if (\is_a($interface, ConfiguredRegisterable::class, allow_string: true)) {
			$this->configureWithObjects($configuration);
			return;
		}
		if (\is_a($interface, Registerable::class, allow_string: true)) {
			$this->configureWithKeys($configuration);
			return;
		}

		throw new CodePathNotSupported(
			message: "$interface must extend Registerable or ConfiguredRegisterable to use RegistryKit.",
			location: 'RegistryKit::configure via ' . self::class,
		);
	}

	/**
	 * Configure the Registry for a Registerable interface.
	 *
	 * @param class-string<Registerable>[] $configuration List of classes to register.
	 * @return void
	 */
	private function configureWithKeys(array $configuration): void {
		foreach ($configuration as $class) {
			$this->library[$class::getKey()] = $class;
		}
	}

	/**
	 * Configure the Registry for a ConfiguredRegisterable interface.
	 *
	 * @param class-string<ConfiguredRegisterable>[] $configuration List of classes to register.
	 * @return void
	 */
	private function configureWithObjects(array $configuration): void {
		foreach ($configuration as $class) {
			$config = $class::getConfiguration();
			$this->configs[$config->key] = $config;
			$this->library[$config->key] = $class;
		}
	}
}
