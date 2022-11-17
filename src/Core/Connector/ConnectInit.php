<?php

namespace Smolblog\Core\Connector;

use Smolblog\App\Environment;
use Smolblog\Core\Command\CommandBus;
use Smolblog\App\Endpoint\{Endpoint, EndpointConfig, EndpointRequest, EndpointResponse, SecurityLevel};

/**
 * Get an Authentication URL for a Connector's provider. The end-user should be
 * redirected to it or shown the URL in some way.
 */
class ConnectInit implements Endpoint {
	/**
	 * Create the endpoint
	 *
	 * @param Environment        $env        Application Environment.
	 * @param ConnectorRegistrar $connectors ConnectorRegistrar to check for provider.
	 * @param CommandBus         $commands   Command handler to kick off the process.
	 */
	public function __construct(
		private Environment $env,
		private ConnectorRegistrar $connectors,
		private CommandBus $commands
	) {
	}

	/**
	 * Configuration for this endpoint
	 *
	 * @return EndpointConfig
	 */
	public static function config(): EndpointConfig {
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

		if (!$this->connectors->has($providerSlug)) {
			return new EndpointResponse(
				statusCode: 404,
				body: ['error' => 'The given provider has not been registered.'],
			);
		}

		if (!isset($request->userId)) {
			return new EndpointResponse(
				statusCode: 400,
				body: ['error' => 'An authenticated user was not provided.'],
			);
		}

		$authUrl = $this->commands->handle(new BeginAuthRequest(
			provider: $providerSlug,
			userId: $request->userId,
			callbackUrl: "{$this->env->apiBase}connect/callback/{$providerSlug}",
		));

		return new EndpointResponse(statusCode: 200, body: ['authUrl' => $authUrl]);
	}
}
