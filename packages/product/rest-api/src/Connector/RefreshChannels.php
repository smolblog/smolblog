<?php

namespace Smolblog\Api\Connector;

use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Api\Verb;
use Smolblog\Core\Connector\Commands\RefreshChannels as RefreshCommand;
use Smolblog\Core\Connector\Queries\ChannelsForConnection;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get an updated list of channels for the given Connection.
 */
class RefreshChannels extends BasicEndpoint {
	/**
	 * Get the configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/connect/connection/{id}/refresh',
			verb: Verb::POST,
			pathVariables: ['id' => ParameterType::identifier()],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus $bus MessageBus for queries and commands.
	 */
	public function __construct(
		private MessageBus $bus,
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @throws NotFound Connection does not exist.
	 *
	 * @param Identifier|null $userId Required; ID of user making the request.
	 * @param array|null      $params Path parameters.
	 * @param object|null     $body   Ignored.
	 * @return Connection
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body = null): Connection {
		$connection = $this->bus->fetch(new ConnectionById($params['id']));
		if (!isset($connection)) {
			throw new NotFound("The connection was not found.");
		}

		$this->bus->dispatch(new RefreshCommand(
			connectionId: $params['id'],
			userId: $userId,
		));

		$channels = $this->bus->fetch(new ChannelsForConnection($params['id']));

		return new Connection(
			id: $connection->id,
			userId: $connection->userId,
			provider: $connection->provider,
			providerKey: $connection->providerKey,
			displayName: $connection->displayName,
			channels: array_map(
				fn($c) => new Channel(id: $c->id, channelKey: $c->channelKey, displayName: $c->displayName),
				$channels
			),
		);
	}
}
