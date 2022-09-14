<?php

namespace Smolblog\Core\Endpoints;

use Smolblog\Core\{Endpoint, EndpointConfig, EndpointRequest, EndpointResponse, Environment};
use Smolblog\Core\Definitions\SecurityLevel;
use Smolblog\Core\Factories\TransientFactory;
use Smolblog\Core\Registrars\ConnectorRegistrar;

/**
 * Get an Authentication URL for a Connector's provider. The end-user should be
 * redirected to it or shown the URL in some way.
 */
class ConnectInit implements Endpoint {
	/**
	 * Initialize this endpoint with its dependencies
	 *
	 * @param Environment        $env        Environment data.
	 * @param ConnectorRegistrar $connectors Connector registry.
	 * @param TransientFactory   $transients Transient factory instance.
	 */
	public function __construct(
		private Environment $env,
		private ConnectorRegistrar $connectors,
		private TransientFactory $transients,
	) {
	}

	/**
	 * Configuration for this endpoint
	 *
	 * @return EndpointConfig
	 */
	public function getConfig(): EndpointConfig {
		return new EndpointConfig(
			route: 'connect/init/[slug]',
			security: SecurityLevel::Registered,
			params: ['slug' => '[a-z0-9-]+']
		);
	}

	/**
	 * Perform the action associated with this endpoint and return the response.
	 *
	 * @param EndpointRequest $request Full information of the HTTP request.
	 * @return EndpointResponse Response to give
	 */
	public function run(EndpointRequest $request): EndpointResponse {
		$providerSlug = $request->params['slug'] ?? null;
		if (!isset($providerSlug)) {
			return new EndpointResponse(
				statusCode: 400,
				body: ['error' => 'A required parameter was not provided.'],
			);
		}

		$connector = $connectors->retrieve($providerSlug);
		if (!isset($connector)) {
			return new EndpointResponse(
				statusCode: 404,
				body: ['error' => 'The given provider has not been registered.'],
			);
		}

		$data = $connector->getInitializationData("{$env->apiBase}connect/callback/$providerSlug");

		$info = [
			...$data->info,
			'user_id' => $request->user->id,
		];
		$transients->setTransient(name: $data->state, value: $info, secondsUntilExpiration: 300);

		return new EndpointResponse(statusCode: 200, body: ['authUrl' => $data->url]);
	}
}
