<?php

namespace Smolblog\IndieWeb\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Api\Verb;
use Smolblog\IndieWeb\Micropub\MicropubService;

/**
 * Media upload endpoint.
 */
class MicropubMedia implements Endpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/micropub/media',
			pathVariables: [
				'site' => ParameterType::identifier(),
			],
			verb: Verb::POST,
			responseShape: ParameterType::string(),
			requiredScopes: [AuthScope::Create],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MicropubService $micropub MicropubService which extends MicropubAdapter.
	 */
	public function __construct(
		private MicropubService $micropub,
	) {
	}

	/**
	 * Handle the endpoint.
	 *
	 * @param ServerRequestInterface $request Incoming request.
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface {
		return $this->micropub->handleMediaEndpointRequest($request);
	}
}
