<?php

namespace Smolblog\Api\Server;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ManualSpec;
use Smolblog\Api\Model;
use Smolblog\Api\ParameterType;
use Smolblog\Framework\Objects\HttpResponse;
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
	 * Construct the endpoint.
	 *
	 * @param ApiEnvironment $env Current environment info.
	 */
	public function __construct(
		private ApiEnvironment $env,
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param ServerRequestInterface $request Incoming request (with optional 'endpoints' attribute).
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface {
		return new HttpResponse(
			body: Model::generateOpenApiSpec(
				apiBase: $this->env->getApiUrl(),
				endpoints: $request->getAttribute('endpoints'),
			)
		);
	}
}
