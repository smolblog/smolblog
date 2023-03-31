<?php

namespace Smolblog\Api\User;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Core\User\User;
use Smolblog\Core\User\UserById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to get standard information about the server.
 */
class GetMyProfile implements Endpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/my/profile',
			requiredScopes: [AuthScope::Read],
		);
	}

	/**
	 * Construct the endpoint
	 *
	 * @param MessageBus $bus MessageBus for queries.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId Required; currently logged in user.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Ignored.
	 * @return User
	 */
	public function run(?Identifier $userId = null, ?array $params = null, ?object $body = null): User {
		return $this->bus->fetch(new UserById($userId));
	}
}
