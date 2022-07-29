<?php

namespace Smolblog\Core\Endpoints;

use Smolblog\Core\{Endpoint, EndpointConfig, Environment};
use Smolblog\Core\Definitions\{HttpVerb, SecurityLevel};
use Smolblog\Core\Registrars\ConnectorRegistrar;
use Smolblog\Core\Toolkits\EndpointToolkit;

/**
 * Get an Authentication URL for a Connector's provider. The end-user should be
 * redirected to it or shown the URL in some way.
 */
class ConnectInit extends Endpoint {
	use EndpointToolkit;

	/**
	 * Initialize this endpoint with its dependencies
	 *
	 * @param Environment        $env        Environment data.
	 * @param ConnectorRegistrar $connectors Connector registry.
	 */
	public function __construct(
		private Environment $env,
		private ConnectorRegistrar $connectors
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
		$env = Environment::get();
		$providerSlug = $request->params['slug'];

		$connector = ConnectorRegistrar::retrieve($providerSlug);
		$data = $connector->getInitializationData($env->getBaseRestUrl() . "connect/callback/$providerSlug");

		$info = [
			...$data->info,
			'user_id' => $request->user->id,
		];
		$env->setTransient(name: $data->state, value: $info, secondsUntilExpiration: 300);

		return new EndpointResponse(statusCode: 200, body: ['authUrl' => $data->url]);
	}
}
