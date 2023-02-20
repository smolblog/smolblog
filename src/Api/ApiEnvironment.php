<?php

namespace Smolblog\Api;

/**
 * Environment-related information needed by the API.
 */
interface ApiEnvironment {
	/**
	 * Get the full URL for the given endpoint.
	 *
	 * @param string $endpoint Endpoint in question.
	 * @return string
	 */
	public function getApiUrl(string $endpoint = '/'): string;
}
