<?php

namespace Smolblog\Api\Connector;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Api\RedirectResponse;
use Smolblog\Api\SuccessResponse;
use Smolblog\Core\Connection\Commands\FinishAuthRequest;
use Smolblog\Core\Connection\Data\AuthRequestStateRepo;
use Smolblog\Core\Connection\Services\ConnectionHandlerRegistry;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Framework\Messages\MessageBus;

/**
 * OAuth callback hook.
 *
 * Endpoint a user is redirected to after authenticating against their external provider. It provides support for both
 * OAuth 1 and OAuth 2 callbacks.
 */
class AuthCallback extends BasicEndpoint {
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
	 * @param CommandBus                $commandBus To dispatch command.
	 * @param ConnectionHandlerRegistry $connectors To check if provider is registered.
	 * @param AuthRequestStateRepo      $authRepo   To check if state is valid.
	 */
	public function __construct(
		private CommandBus $commandBus,
		private ConnectionHandlerRegistry $connectors,
		private AuthRequestStateRepo $authRepo,
	) {
	}

	/**
	 * Run the endpoint
	 *
	 * This is a public endpoint as there is not always a way to ensure authentication carries through the entire
	 * OAuth process.
	 *
	 * @throws BadRequest Invalid parameters given.
	 *
	 * @param Identifier|null $userId Authenticated user; ignored.
	 * @param array|null      $params Parameters for the endpoint.
	 * @param object|null     $body   Ignored.
	 * @return SuccessResponse
	 */
	public function run(
		?Identifier $userId = null,
		?array $params = [],
		?object $body = null
	): SuccessResponse|RedirectResponse {
		$state = $params['state'] ?? $params['oauth_token'] ?? null;
		$code = $params['code'] ?? $params['oauth_verifier'] ?? null;

		if (!$state) {
			throw new BadRequest('No valid state or oauth_token was given');
		}
		if (!$code) {
			throw new BadRequest('No valid code or oauth_verifier was given');
		}

		$command = new FinishAuthRequest(
			provider: $params['provider'],
			stateKey: $state,
			code: $code,
		);
		$this->commandBus->execute($command);

		return isset($command->returnToUrl) ? new RedirectResponse(url: $command->returnValue()) : new SuccessResponse();
	}
}
