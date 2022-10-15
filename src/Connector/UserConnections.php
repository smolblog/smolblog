<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Endpoint\{Endpoint, EndpointConfig, EndpointRequest, EndpointResponse, SecurityLevel};

/**
 * Get an Authentication URL for a Connector's provider. The end-user should be
 * redirected to it or shown the URL in some way.
 */
class UserConnections implements Endpoint {
	public function __construct(
		private ConnectionReader $connectionRepo,
		private ChannelReader $channelRepo,
	) {
	}

	/**
	 * Configuration for this endpoint
	 *
	 * @return EndpointConfig
	 */
	public static function config(): EndpointConfig {
		return new EndpointConfig(
			route: 'my/connections',
			security: SecurityLevel::Registered
		);
	}

	/**
	 * Perform the action associated with this endpoint and return the response.
	 *
	 * @param EndpointRequest $request Full information of the HTTP request.
	 * @return EndpointResponse Response to give
	 */
	public function run(EndpointRequest $request): EndpointResponse {
		if (!isset($request->userId)) {
			return new EndpointResponse(
				statusCode: 400,
				body: ['error' => 'An authenticated user was not provided.'],
			);
		}

		$connections = $this->connectionRepo->getConnectionsForUser($request->userId);
		$channels = $this->channelRepo->getChannelsForConnections(array_map(fn($c) => $c->id, $connections));

		$response = array_map(fn($con) => [
			'id' => $con->id,
			'provider' => $con->provider,
			'displayName' => $con->displayName,
			'channels' => array_map(fn($cha) => [
				'id' => $cha->id,
				'displayName' => $cha->displayName,
			], $channels[$con->id]),
		], $connections);

		return new EndpointResponse(statusCode: 200, body: ['connections' => $response]);
	}
}
