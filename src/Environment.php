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
	 */
	public function __construct(
		public readonly string $apiBase
	) {
	}
}
