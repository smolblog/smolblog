<?php

namespace Smolblog\Api\ActivityPub;

use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Site\SiteById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Endpoint to give an ActivityPub actor for a site.
 */
class GetActor implements Endpoint {
	/**
	 * Get endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/activitypub/actor',
			pathVariables: ['site' => ParameterType::identifier()],
			requiredScopes: [],
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
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId Ignored.
	 * @param array|null      $params Expects 'site'.
	 * @param object|null     $body   Ignored.
	 * @return ActorResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): ActorResponse {
		$site = $this->bus->fetch(new SiteById($params['site']));

		return new ActorResponse(
			id: $this->env->getApiUrl("/site/$site->id/activitypub/actor"),
			type: ActorType::Person,
			inbox: $this->env->getApiUrl("/site/$site->id/activitypub/inbox"),
			outbox: $this->env->getApiUrl("/site/$site->id/activitypub/outbox"),
			preferredUsername: $site->handle,
			name: $site->displayName,
			summary: $site->description,
			sharedInbox: $this->env->getApiUrl("/activitypub/inbox"),
			publicKeyPem: $site->publicKey,
		);
	}
}
