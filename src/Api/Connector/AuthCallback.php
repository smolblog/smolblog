<?php

namespace Smolblog\Api\Connector;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\DataType;
use Smolblog\Api\ErrorResponses;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;

/**
 * OAuth callback hook.
 *
 * Endpoint a user is redirected to after authenticating against their external provider. It provides support for both
 * OAuth 1 and OAuth 2 callbacks.
 */
class AuthCallback implements Endpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: 'connect/callback/{provider}',
			pathVariables: ['provider' => ParameterType::string(pattern: '^[a-zA-Z0-9]+$')],
			queryVariables: [
				'state' => ParameterType::string(),
				'code' => ParameterType::string(),
				'oauth_token' => ParameterType::string(),
				'oauth_verifier' => ParameterType::string(),
			],
			public: true,
		);
	}

	/**
	 * Run the endpoint
	 *
	 * This is a public endpoint as there is not always a way to ensure authentication carries through the entire
	 * OAuth process.
	 *
	 * @throws NotFound Provider not registered.
	 * @throws BadRequest Invalid parameters given.
	 *
	 * @param Identifier|null $userId Authenticated user; ignored.
	 * @param array           $params Parameters for the endpoint.
	 * @param array           $body   Ignored.
	 * @return ConnectionEstablishedResponse
	 */
	public function run(
		?Identifier $userId = null,
		array $params = [],
		array $body = []
	): ConnectionEstablishedResponse {
		if (!$params['provider']) {
			throw new NotFound('The given provider has not been registered.');
		}
		if (!$params['state'] && !$params['oauth_token']) {
			throw new BadRequest('No valid state or oauth_token was given');
		}

		return new ConnectionEstablishedResponse(
			id: Identifier::createRandom(),
			provider: 'smolblog',
			displayName: 'snek@smolblog.org',
			channels: ['snek.smol.blog', 'birb.smol.blog'],
		);
	}
}
