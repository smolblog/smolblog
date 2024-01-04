<?php

namespace Smolblog\ActivityPub\Api;

use DateTimeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Site\SiteById;
use Smolblog\Framework\ActivityPub\Objects\Actor;
use Smolblog\Framework\ActivityPub\Objects\ActorType;
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
			responseShape: ParameterType::object(
				id: ParameterType::string(format: 'url'),
				inbox: ParameterType::string(format: 'url'),
				outbox: ParameterType::string(format: 'url'),
				preferredUsername: ParameterType::string(),
				url: ParameterType::string(format: 'url'),
				name: ParameterType::string(),
				summary: ParameterType::string(),
				endpoints: ParameterType::object(
					sharedInbox: ParameterType::string(format: 'url'),
				),
				publicKey: ParameterType::object(
					id: ParameterType::string(format: 'url'),
					owner: ParameterType::string(format: 'url'),
					publicKeyPem: ParameterType::string(),
				),
			),
		);
	}

	/**
	 * Construct the endpoint
	 *
	 * @param MessageBus      $bus MessageBus for queries.
	 * @param ApiEnvironment  $env API environment.
	 * @param LoggerInterface $log Logger to log requests.
	 */
	public function __construct(
		private MessageBus $bus,
		private ApiEnvironment $env,
		private LoggerInterface $log,
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
		$this->log->debug(
			message: 'ActivityPub Actor endpoint ' . date(DateTimeInterface::COOKIE),
			context: [
				'method' => $request->getMethod(),
				'query' => $request->getQueryParams(),
				'body' => $request->getBody()->getContents(),
			],
		);

		$site = $this->bus->fetch(new SiteById(
			Identifier::fromString($request->getAttribute('smolblogPathVars', [])['site'])
		));

		if ($request->hasHeader('Accept') && !str_contains($request->getHeaderLine('Accept'), 'json')) {
			return new HttpResponse(code: 302, headers: ['Location' => $site->baseUrl]);
		}

		$response = new Actor(
			id: $this->env->getApiUrl("/site/$site->id/activitypub/actor"),
			type: ActorType::Person,
			inbox: $this->env->getApiUrl("/site/$site->id/activitypub/inbox"),
			outbox: $this->env->getApiUrl("/site/$site->id/activitypub/outbox"),
			preferredUsername: $site->handle,
			url: $site->baseUrl,
			name: $site->displayName,
			summary: $site->description,
			endpoints: ['sharedInbox' => $this->env->getApiUrl("/activitypub/inbox")],
			publicKeyPem: $site->publicKey,
		);

		return new HttpResponse(body: $response);
	}
}
