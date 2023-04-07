<?php

namespace Smolblog\Api\Site;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Site\SiteUsers;
use Smolblog\Core\User\User;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to get users attached to a site. Requires author permissions on the site.
 */
class GetUsers implements Endpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/users',
			pathVariables: [
				'site' => ParameterType::identifier(),
			],
			responseShape: ParameterType::object(
				users: ParameterType::required(ParameterType::array(
					items: ParameterType::object(
						user: ParameterType::fromClass(User::class),
						isAdmin: ParameterType::boolean(),
						isAuthor: ParameterType::boolean(),
					),
				)),
			),
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
	 * @param Identifier  $userId Required; currently logged in user.
	 * @param array       $params Expects site parameter.
	 * @param object|null $body   Ignored.
	 * @return SiteSettings
	 */
	public function run(?Identifier $userId = null, ?array $params = null, ?object $body = null): GenericResponse {
		return new GenericResponse(
			users: $this->bus->fetch(new SiteUsers(siteId: $params['site'], userId: $userId))
		);
	}
}
