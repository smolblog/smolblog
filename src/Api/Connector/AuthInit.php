<?php

namespace Smolblog\Api\Connector;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;

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
			route: '/connect/init/{provider}',
			pathVariables: ['provider' => ParameterType::string(pattern: '/[a-z0-9]+/i')],
			public: false,
			responseShape: ParameterType::object([
				'url' => ParameterType::required(ParameterType::string(format: 'url'))
			]),
		);
	}

	/**
	 * Perform the endpoint
	 *
	 * @throws NotFound Provider not registered.
	 *
	 * @param Identifier|null $userId Authenticated user's ID.
	 * @param array           $params Ignored.
	 * @param array           $body   Ignored.
	 * @return Value
	 */
	public function run(?Identifier $userId, array $params = [], array $body = []): Value {
		if (!$params['provider']) {
			throw new NotFound('The given provider has not been registered.');
		}

		return new GenericResponse(url: '//tumblr.com/auth/');
	}
}
