<?php

namespace Smolblog\Api\Server;

use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to get standard information about the server.
 */
class Base extends BasicEndpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/',
			requiredScopes: [],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param ApiEnvironment $env API Environment info.
	 */
	public function __construct(
		private ApiEnvironment $env,
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId Ignored.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Ignored.
	 * @return ServerInfo
	 */
	public function run(?Identifier $userId = null, ?array $params = null, ?object $body = null): ServerInfo {
		return new ServerInfo(
			serverVersion: '0.2.0-alpha',
			specHref: $this->env->getApiUrl('/spec'),
		);
	}
}
