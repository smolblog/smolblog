<?php

namespace Smolblog\Core\Dependencies;

/**
 * Environment information for the App
 */
class Environment {
	/**
	 * Load the information in
	 *
	 * @param string $apiBase Base URL (including scheme) for the REST API.
	 */
	public function __construct(
		public readonly string $apiBase
	) {
	}
}
