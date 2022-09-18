<?php

namespace Smolblog\Core;

/**
 * Environment information for the App
 */
class Environment {
	/**
	 * Load the information in
	 *
	 * @param string $apiBase Base URL (including scheme and trailing slash) for the REST API.
	 * @param array  $envVars Associative array of arbitrary environment variables.
	 */
	public function __construct(
		public readonly string $apiBase,
		private array $envVars = []
	) {
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
}
