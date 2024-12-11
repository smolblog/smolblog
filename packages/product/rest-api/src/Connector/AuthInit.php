<?php

namespace Smolblog\Api\Connector;

use Smolblog\Api\ApiEnvironment;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Connection\Commands\BeginAuthRequest;
use Smolblog\Foundation\Service\Command\CommandBus;

/**
 * Kick off an OAuth request to an external provider.
 */
class AuthInit extends BasicEndpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/connect/init/{provider}',
			pathVariables: ['provider' => ParameterType::string(pattern: '^[a-zA-Z0-9]+$')],
			queryVariables: ['returnTo' => ParameterType::string(format: 'url')],
			responseShape: ParameterType::object(
				url: ParameterType::required(ParameterType::string(format: 'url'))
			),
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param CommandBus     $bus CommandBus for sending the command.
	 * @param ApiEnvironment $env Environment information.
	 */
	public function __construct(
		private CommandBus $bus,
		private ApiEnvironment $env,
	) {
	}

	/**
	 * Perform the endpoint
	 *
	 * @param Identifier|null $userId Authenticated user's ID.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Ignored.
	 * @return array
	 */
	public function run(?Identifier $userId, ?array $params = null, ?object $body = null): array {
		$command = new BeginAuthRequest(
			provider: $params['provider'],
			userId: $userId ?? Identifier::nil(),
			callbackUrl: $this->env->getApiUrl('/connect/callback/' . $params['provider']),
			returnToUrl: $params['returnTo'] ?? null,
		);

		$this->bus->execute($command);

		return ['url' => $command->returnValue()];
	}
}
