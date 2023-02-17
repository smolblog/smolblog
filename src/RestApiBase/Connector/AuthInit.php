<?php

namespace Smolblog\RestApiBase\Connector;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\RestApiBase\Endpoint;
use Smolblog\RestApiBase\EndpointConfig;
use Smolblog\RestApiBase\ParameterType;
use Smolblog\RestApiBase\Response;
use Smolblog\RestApiBase\Verb;

/**
 * Kick off an OAuth request to an external provider.
 */
class AuthInit implements Endpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: 'connect/init/{provider}',
			pathVariables: ['provider' => ParameterType::string(pattern: '/[a-z0-9]+/i')],
			public: false,
		);
	}

	/**
	 * Perform the endpoint
	 *
	 * @param Identifier|null $userId Authenticated user's ID.
	 * @param array           $params Ignored.
	 * @param array           $body   Ignored.
	 * @return EndpointConfig
	 */
	public function run(?Identifier $userId, array $params, array $body): EndpointConfig {
		// return new class ('//tumblr.com/user/auth') extends Response {
		// 	public function __construct(public readonly string $url) {
		// 	}
		// };
		return self::getConfiguration();
	}
}
