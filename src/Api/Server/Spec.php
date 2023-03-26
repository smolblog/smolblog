<?php

namespace Smolblog\Api\Server;

use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\Model;
use Smolblog\Api\ParameterType;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Return the generated OpenAPI spec.
 */
class Spec implements Endpoint {
	/**
	 * Get configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/spec',
			responseShape: ParameterType::string(),
			requiredScopes: [],
		);
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId Ignored.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Ignored.
	 * @return Value
	 */
	public function run(?Identifier $userId = null, ?array $params = null, ?object $body = null): Value {
		return new GenericResponse(...Model::generateOpenApiSpec());
	}
}
