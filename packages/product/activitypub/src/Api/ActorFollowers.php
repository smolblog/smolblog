<?php

namespace Smolblog\ActivityPub\Api;

use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Federation\GetFollowersForSiteByProvider;
use Smolblog\Framework\ActivityPub\Objects\Collection;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get the number of ActivityPub followers for a given Actor.
 *
 * In keeping with Mastodon, this does not list the followers. Use the authenticated `/site/{site}/followers`
 * endpoint to manage followers.
 */
class ActorFollowers extends BasicEndpoint {
	/**
	 * Get endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/activitypub/followers',
			pathVariables: ['site' => ParameterType::identifier()],
			requiredScopes: [],
			responseShape: ParameterType::object(
				id: ParameterType::string(format: 'url'),
				type: ParameterType::string(),
				totalItems: ParameterType::number(),
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
	 * @return Collection
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body = null): Collection {
		$siteId = $params['site'];
		$allFollowers = $this->bus->fetch(new GetFollowersForSiteByProvider($siteId));

		return new Collection(
			id: $this->env->getApiUrl("/site/$siteId/activitypub/followers"),
			totalItems: count($allFollowers['activitypub'] ?? []),
		);
	}
}
