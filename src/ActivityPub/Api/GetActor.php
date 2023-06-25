<?php

namespace Smolblog\ActivityPub\Api;

use Smolblog\ActivityPhp\Type\Extended\Actor\Person;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Api\RedirectResponse;
use Smolblog\Core\Site\SiteById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Endpoint to give an ActivityPub actor for a site.
 */
class GetActor extends BasicEndpoint {
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
	 * @return Person|RedirectResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): Person|RedirectResponse {
		$site = $this->bus->fetch(new SiteById($params['site']));

		if (isset($params['Accept']) && !str_contains($params['Accept'], 'json')) {
			return new RedirectResponse($site->baseUrl);
		}

		$response = new Person();
		$response->id = $this->env->getApiUrl("/site/$site->id/activitypub/actor");
		$response->inbox = $this->env->getApiUrl("/site/$site->id/activitypub/inbox");
		$response->outbox = $this->env->getApiUrl("/site/$site->id/activitypub/outbox");
		$response->preferredUsername = $site->handle;
		$response->url = $site->baseUrl;
		$response->name = $site->displayName;
		$response->summary = $site->description;
		$response->endpoints = ['sharedInbox' => $this->env->getApiUrl("/activitypub/inbox")];
		$response->publicKey = [
			'id' => $response->id . '#publicKey',
			'owner' => $response->id,
			'publicKeyPem' => $site->publicKey,
		];

		return $response;
	}
}
