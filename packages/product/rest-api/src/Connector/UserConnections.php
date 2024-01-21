<?php

namespace Smolblog\Api\Connector;

use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Connector\Queries\ChannelsForConnection;
use Smolblog\Core\Connector\Queries\ConnectionsForUser;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Get all connections for a user along with their channels.
 */
class UserConnections extends BasicEndpoint {
	/**
	 * Get the configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/my/connections',
			requiredScopes: [AuthScope::Admin],
			responseShape: ParameterType::object(
				connections: ParameterType::required(ParameterType::array(
					items: ParameterType::fromClass(Connection::class),
				))
			),
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus $bus MessageBus for sending queries.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId User making the request.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Ignored.
	 * @return GenericResponse
	 */
	public function run(?Identifier $userId, ?array $params = null, ?object $body = null): GenericResponse {
		return new GenericResponse(connections: array_map(
			fn($con) => new Connection(
				id: $con->id,
				userId: $con->userId,
				provider: $con->provider,
				providerKey: $con->providerKey,
				displayName: $con->displayName,
				channels: array_map(
					fn($cha) => new Channel(
						id: $cha->id,
						channelKey: $cha->channelKey,
						displayName: $cha->displayName,
					),
					$this->bus->fetch(new ChannelsForConnection($con->id)) ?? []
				)
			),
			$this->bus->fetch(new ConnectionsForUser(userId: $userId)) ?? []
		));
	}
}
