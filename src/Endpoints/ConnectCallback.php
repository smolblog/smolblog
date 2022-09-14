<?php

namespace Smolblog\Core\Endpoints;

use Smolblog\Core\{Endpoint, EndpointConfig, EndpointRequest, EndpointResponse, Environment};
use Smolblog\Core\Factories\TransientFactory;
use Smolblog\Core\Registrars\ConnectorRegistrar;

/**
 * Endpoint to handle an OAuth2 callback from a Connector's provider
 */
class ConnectCallback implements Endpoint {
	/**
	 * Create the endpoint
	 *
	 * @param ConnectorRegistrar $connectors Connector Registrar.
	 * @param TransientFactory   $transients Transient factory.
	 */
	public function __construct(
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
			route: 'connect/callback/[slug]',
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
		if (!isset($request->params['slug']) || !isset($request->params['state']) || !isset($request->params['code'])) {
			return new EndpointResponse(
				statusCode: 400,
				body: ['error' => 'A required parameter was not provided.'],
			);
		}

		$connector = $this->connectors->retrieve($request->params['slug']);
		if (!isset($connector)) {
			return new EndpointResponse(
				statusCode: 404,
				body: ['error' => 'The given provider has not been registered.'],
			);
		}

		$info = $transients->getTransient(name: $request->params['state']);
		if (!isset($info)) {
			return new EndpointResponse(
				statusCode: 400,
				body: ['error' => 'A matching request was not found; please try again.'],
			);
		}

		$credential = $connector->createCredential(code: $request->params['code'], info: $info);

		return new EndpointResponse(statusCode: 200, body: ['credentialId' => $credential->id]);
	}
}
