<?php

namespace Smolblog\Api\Connector;

use Smolblog\Api\AuthScope;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\DataType;
use Smolblog\Api\ErrorResponses;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Core\Connector\Commands\FinishAuthRequest;
use Smolblog\Core\Connector\Services\AuthRequestStateRepo;
use Smolblog\Core\Connector\Services\ConnectorRegistrar;
use Smolblog\Framework\Messages\MessageBus;

/**
 * OAuth callback hook.
 *
 * Endpoint a user is redirected to after authenticating against their external provider. It provides support for both
 * OAuth 1 and OAuth 2 callbacks.
 */
class AuthCallback implements Endpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: 'connect/callback/{provider}',
			pathVariables: ['provider' => ParameterType::string(pattern: '^[a-zA-Z0-9]+$')],
			queryVariables: [
				'state' => ParameterType::string(),
				'code' => ParameterType::string(),
				'oauth_token' => ParameterType::string(),
				'oauth_verifier' => ParameterType::string(),
			],
			requiredScopes: [],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus           $bus        To dispatch command.
	 * @param ConnectorRegistrar   $connectors To check if provider is registered.
	 * @param AuthRequestStateRepo $authRepo   To check if state is valid.
	 */
	public function __construct(
		private MessageBus $bus,
		private ConnectorRegistrar $connectors,
		private AuthRequestStateRepo $authRepo,
	) {
	}

	/**
	 * Run the endpoint
	 *
	 * This is a public endpoint as there is not always a way to ensure authentication carries through the entire
	 * OAuth process.
	 *
	 * @throws NotFound Provider not registered.
	 * @throws BadRequest Invalid parameters given.
	 *
	 * @param Identifier|null $userId Authenticated user; ignored.
	 * @param array|null      $params Parameters for the endpoint.
	 * @param object|null     $body   Ignored.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId = null, ?array $params = [], ?object $body = null): SuccessResponse {
		if (empty($params['provider']) || !$this->connectors->has($params['provider'])) {
			throw new NotFound('The given provider has not been registered.');
		}

		$state = $params['state'] ?? $params['oauth_token'] ?? null;
		$code = $params['code'] ?? $params['oauth_verifier'] ?? null;

		if (!$state) {
			throw new BadRequest('No valid state or oauth_token was given');
		}
		if (!$code) {
			throw new BadRequest('No valid code or oauth_verifier was given');
		}

		if ($this->authRepo->getAuthRequestState($state) === null) {
			throw new NotFound('The given authentication session was not found. It may be incorrect or expired.');
		}

		$this->bus->dispatch(new FinishAuthRequest(
			provider: $params['provider'],
			stateKey: $state,
			code: $code,
		));

		return new SuccessResponse();
	}
}
