<?php

namespace Smolblog\App\Endpoints;

use Smolblog\App\Endpoint\{Endpoint, EndpointConfig, EndpointRequest, EndpointResponse};
use Smolblog\Core\Connector\Entities\AuthRequestStateReader;
use Smolblog\Core\Connector\ConnectorRegistrar;
use Smolblog\Core\Connector\Commands\FinishAuthRequest;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Framework\Executor;

/**
 * Endpoint to handle an OAuth2 callback from a Connector's provider
 */
class ConnectCallback implements Endpoint {
	/**
	 * Create the endpoint
	 *
	 * @param ConnectorRegistrar     $connectors Connector Registrar.
	 * @param AuthRequestStateReader $stateRepo  State repository.
	 * @param Executor               $commands   Command bus.
	 */
	public function __construct(
		private ConnectorRegistrar $connectors,
		private AuthRequestStateReader $stateRepo,
		private Executor $commands,
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
		// Add compatability with OAuth 1 callbacks.
		$state = $request->params['state'] ?? $request->params['oauth_token'] ?? null;
		$code = $request->params['code'] ?? $request->params['oauth_verifier'] ?? null;

		if (!isset($request->params['slug']) || !isset($state) || !isset($code)) {
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

		if (!$this->stateRepo->has(id: AuthRequestState::buildId(key: $state))) {
			return new EndpointResponse(
				statusCode: 400,
				body: ['error' => 'A matching request was not found; please try again.'],
			);
		}

		$this->commands->exec(new FinishAuthRequest(
			provider: $request->params['slug'],
			stateKey: $state,
			code: $code,
		));

		return new EndpointResponse(statusCode: 200, body: ['success' => 'true']);
	}
}
