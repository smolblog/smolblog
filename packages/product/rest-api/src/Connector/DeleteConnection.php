<?php

namespace Smolblog\Api\Connector;

use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\Connector\Commands\DeleteConnection as DeleteConnectionCommand;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Delete a connection.
 */
class DeleteConnection extends BasicEndpoint {
	/**
	 * Get the endpoint's configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/connect/connection/{id}/delete',
			verb: Verb::DELETE,
			pathVariables: ['id' => ParameterType::identifier()],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus $bus MessageBus for queries and commands.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @throws NotFound Connection was not found.
	 *
	 * @param Identifier|null $userId Required. User making the change.
	 * @param array|null      $params Connection ID expected.
	 * @param object|null     $body   Ignored.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body = null): SuccessResponse {
		if ($this->bus->fetch(new ConnectionById($params['id'])) === null) {
			throw new NotFound('The given Connection was not found.');
		}

		$this->bus->dispatch(new DeleteConnectionCommand(userId: $userId, connectionId: $params['id']));

		return new SuccessResponse();
	}
}
