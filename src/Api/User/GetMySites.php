<?php

namespace Smolblog\Api\User;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Site\Site;
use Smolblog\Core\User\UserSites;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Get a list of the user's sites.
 */
class GetMySites implements Endpoint {
	/**
	 * Get the endpont configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/my/sites',
			responseShape: ParameterType::object(
				sites: ParameterType::required(ParameterType::array(
					items: ParameterType::fromClass(Site::class)
				))
			),
			requiredScopes: [AuthScope::Read]
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
	 * Execute the endpoint
	 *
	 * @param Identifier|null $userId Required; user whose sites to list.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Ignored.
	 * @return GenericResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): GenericResponse {
		return new GenericResponse(sites: $this->bus->fetch(new UserSites($userId)));
	}
}
