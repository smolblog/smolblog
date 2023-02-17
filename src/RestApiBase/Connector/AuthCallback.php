<?php

namespace Smolblog\RestApiBase\Connector;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;
use Smolblog\RestApiBase\Endpoint;
use Smolblog\RestApiBase\EndpointConfig;
use Smolblog\RestApiBase\ParameterType;

class AuthCallback implements Endpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: 'connect/callback/{provider}',
			pathVariables: ['provider' => ParameterType::string(pattern: '/[a-z0-9]+/i')],
			queryVariables: [
				'state' => ParameterType::string(),
				'code' => ParameterType::string(),
				'oauth_token' => ParameterType::string(),
				'oauth_verifier' => ParameterType::string(),
			],
			public: true,
		);
	}

	public function run(?Identifier $userId, array $params, array $body): ConnectionEstablishedResponse
	{
		return new ConnectionEstablishedResponse(
			id: Identifier::createRandom(),
			provider: 'smolblog',
			displayName: 'snek@smolblog.org',
			channels: ['snek.smol.blog', 'birb.smol.blog'],
		);
	}
}
