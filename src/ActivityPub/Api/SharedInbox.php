<?php

namespace Smolblog\ActivityPub\Api;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Smolblog\ActivityPub\InboxService;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Verb;
use Smolblog\Framework\ActivityPub\Objects\ActivityPubObject;
use Smolblog\Framework\Objects\HttpResponse;

/**
 * Server-wide ActivityPub Inbox endpoint.
 */
class SharedInbox implements Endpoint {
	/**
	 * Get endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/activitypub/inbox',
			verb: Verb::POST,
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
