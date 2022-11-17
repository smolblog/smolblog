<?php

namespace Smolblog\App;

use JsonSerializable;

/**
 * Environment information for the App
 */
class Environment implements JsonSerializable {
	/**
	 * Base URL (including scheme and trailing slash) for the REST API.
	 *
	 * @var string
	 */
	public readonly string $apiBase;

	/**
	 * Additional environment variables defined at startup.
	 *
	 * @var array
	 */
	private array $envVars = [];

	/**
	 * Load the information in
	 *
	 * @param string $apiBase    Base URL (including scheme and trailing slash) for the REST API.
	 * @param mixed  ...$envVars Arbitrary environment variables.
	 */
	public function __construct(string $apiBase, mixed ...$envVars) {
		$this->apiBase = rtrim($apiBase, '/') . '/';
		$this->envVars = $envVars;
	}

	/**
	 * Quick access for any added environment variables.
	 *
	 * @param string $name Environment variable to get.
	 * @return ?string
	 */
	public function __get(string $name): ?string {
		return $this->envVars[$name] ?? null;
	}

	/**
	 * Override `__set` to do nothing.
	 *
	 * @param string $name  Variable to set.
	 * @param mixed  $value Value to provide.
	 * @return void
	 */
	public function __set(string $name, mixed $value): void {
		$trace = debug_backtrace();
		trigger_error(
			'Attempt to set readonly property ' . $name .
			' in ' . $trace[0]['file'] .
			' on line ' . $trace[0]['line'],
			E_USER_ERROR
		);
	}

	/**
	 * Get all defined fields as a single array.
	 *
	 * @return array
	 */
	public function toArray(): array {
		return ['apiBase' => $this->apiBase, ...$this->envVars];
	}

	/**
	 * Same as toArray()
	 *
	 * @return mixed
	 */
	public function jsonSerialize(): mixed {
		return $this->toArray();
	}
}
