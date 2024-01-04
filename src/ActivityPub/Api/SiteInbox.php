<?php

namespace Smolblog\ActivityPub\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smolblog\ActivityPub\InboxService;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Api\Verb;
use Smolblog\Framework\ActivityPub\Objects\ActivityPubObject;
use Smolblog\Framework\Objects\HttpResponse;

/**
 * ActivityPub Inbox endpoint.
 */
class SiteInbox implements Endpoint {
	/**
	 * Get endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/activitypub/inbox',
			verb: Verb::POST,
			pathVariables: ['site' => ParameterType::identifier()],
			bodyClass: ActivityPubObject::class,
			requiredScopes: [],
		);
	}

	/**
	 * Create the endpoint.
	 *
	 * @param InboxService $service Service to handle the request.
	 */
	public function __construct(
		private InboxService $service,
	) {
	}

	/**
	 * Handle the incoming request.
	 *
	 * @param ServerRequestInterface $request Incoming request.
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface {
		$this->service->handleRequest($request);
		return new HttpResponse(code: 204);
	}
}
