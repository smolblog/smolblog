<?php

namespace Smolblog\Api;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Base Endpoint interface
 *
 * The objective here is to define a REST API endpoint in a platform-agnostic way. This may not be an abstraction that
 * works forever, but it will hopefully last long enough to abstract WordPress away...
 */
interface Endpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig;

	/**
	 * Respond to the request.
	 *
	 * @param Identifier|null $userId ID of the authenticated user; null if no logged-in user.
	 * @param array           $params Associative array of any parameters in the URL or query string.
	 * @param array           $body   Array-decoded JSON body if present.
	 * @return Value
	 */
	public function run(
		?Identifier $userId,
		array $params,
		array $body,
	): Value;
}
