<?php

namespace Smolblog\Core\Connector;

use Smolblog\App\Environment;
use Smolblog\Core\Command\CommandBus;
use Smolblog\Core\Endpoint\{Endpoint, EndpointConfig, EndpointRequest, EndpointResponse};

/**
 * Endpoint to handle an OAuth2 callback from a Connector's provider
 */
class ConnectCallback implements Endpoint {
	/**
	 * Create the endpoint
	 *
	 * @param ConnectorRegistrar     $connectors Connector Registrar.
	 * @param AuthRequestStateReader $stateRepo  State repository.
	 * @param CommandBus             $commands   Command bus.
	 */
	public function __construct(
		private ConnectorRegistrar $connectors,
		private AuthRequestStateReader $stateRepo,
		private CommandBus $commands,
	) {
	}

	/**
	 * Configuration for this endpoint
	 *
	 * @return EndpointConfig
	 */
	public static function config(): EndpointConfig {
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

		if (!$this->connectors->has($request->params['slug'])) {
			return new EndpointResponse(
				statusCode: 404,
				body: ['error' => 'The given provider has not been registered.'],
			);
		}

		if (!$this->stateRepo->has(id: $request->params['state'])) {
			return new EndpointResponse(
				statusCode: 400,
				body: ['error' => 'A matching request was not found; please try again.'],
			);
		}

		$this->commands->handle(new FinishAuthRequest(
			provider: $request->params['slug'],
			stateKey: $request->params['state'],
			code: $request->params['code'],
		));

		return new EndpointResponse(statusCode: 200, body: ['success' => 'true']);
	}
}
