<?php

namespace Smolblog\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
	 * Handle the endpoint.
	 *
	 * @param ServerRequestInterface $request Incoming request with smolblogUserId and smolblogPathVars set.
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface;
}
