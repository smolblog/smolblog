<?php

namespace Smolblog\ActivityPub\Api;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Smolblog\ActivityPhp\Type\Extended\Actor\Person;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Site\SiteById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\HttpResponse;
use Smolblog\Framework\Objects\Identifier;

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
	 * If the `Accept` header does not contain 'json', the endpoint will redirect to the site homepage. This works
	 * around Mastodon ignoring the `url` property and linking to the `ID` (this endpoint) for the user profile.
	 *
	 * @param ServerRequestInterface $request Incoming request.
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface {
		$site = $this->bus->fetch(new SiteById(
			Identifier::fromString($request->getAttribute('smolblogPathVars', [])['site'])
		));

		if ($request->hasHeader('Accept') && !str_contains($request->getHeaderLine('Accept'), 'json')) {
			return new HttpResponse(code: 302, headers: ['Location' => $site->baseUrl]);
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

		return new HttpResponse(body: $response->toArray());
	}
}
