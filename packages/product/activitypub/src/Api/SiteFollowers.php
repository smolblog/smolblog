<?php

namespace Smolblog\ActivityPub\Api;

use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Federation\Follower;
use Smolblog\Core\Federation\GetFollowersForSiteByProvider;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get the followers for a given Site.
 */
class SiteFollowers extends BasicEndpoint {
	/**
	 * Get endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/followers',
			pathVariables: ['site' => ParameterType::identifier()],
			requiredScopes: [AuthScope::Create],
			responseShape: ParameterType::object(
				total: ParameterType::number(),
				followers: ParameterType::array(ParameterType::fromClass(Follower::class)),
			),
		);
	}

	/**
	 * Construct the endpoint
	 *
	 * @param MessageBus     $bus MessageBus for queries.
	 * @param ApiEnvironment $env API environment.
	 */
	public function __construct(
		private MessageBus $bus,
		private ApiEnvironment $env,
	) {
	}

	/**
	 * Run the endpoint.
	 *
	 * @throws NotFound When the resource does not exist here.
	 *
	 * @param Identifier  $userId Ignored.
	 * @param array|null  $params Expects 'site'.
	 * @param object|null $body   Ignored.
	 * @return GenericResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body = null): GenericResponse {
		$siteId = $params['site'];
		$sortedFollowers = $this->bus->fetch(new GetFollowersForSiteByProvider($siteId));
		$flattened = array_reduce($sortedFollowers, fn($carry, $item) => array_merge($carry, $item), []);

		return new GenericResponse(
			total: count($flattened),
			followers: $flattened,
		);
	}
}
