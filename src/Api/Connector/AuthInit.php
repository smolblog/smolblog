<?php

namespace Smolblog\Api\Connector;

use Smolblog\Api\ApiEnvironment;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Connector\Commands\BeginAuthRequest;
use Smolblog\Core\Connector\Services\ConnectorRegistry;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Kick off an OAuth request to an external provider.
 */
class AuthInit implements Endpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/connect/init/{provider}',
			pathVariables: ['provider' => ParameterType::string(pattern: '^[a-zA-Z0-9]+$')],
			responseShape: ParameterType::object(
				url: ParameterType::required(ParameterType::string(format: 'url'))
			),
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus        $bus        MessageBus for sending the command.
	 * @param ConnectorRegistry $connectors Check param against registered Connectors.
	 * @param ApiEnvironment    $env        Environment information.
	 */
	public function __construct(
		private MessageBus $bus,
		private ConnectorRegistry $connectors,
		private ApiEnvironment $env,
	) {
	}

	/**
	 * Perform the endpoint
	 *
	 * @throws NotFound Provider not registered.
	 *
	 * @param Identifier|null $userId Authenticated user's ID.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Ignored.
	 * @return Value
	 */
	public function run(?Identifier $userId, ?array $params = null, ?object $body = null): Value {
		if (empty($params['provider']) || !$this->connectors->has($params['provider'])) {
			throw new NotFound('The given provider has not been registered.');
		}

		$command = new BeginAuthRequest(
			provider: $params['provider'],
			userId: $userId,
			callbackUrl: $this->env->getApiUrl('/connect/callback/' . $params['provider']),
		);

		$this->bus->dispatch($command);

		return new GenericResponse(url: $command->redirectUrl);
	}
}
