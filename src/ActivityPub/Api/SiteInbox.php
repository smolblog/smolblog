<?php

namespace Smolblog\ActivityPub\Api;

use Smolblog\ActivityPhp\Type\Extended\Activity\Follow;
use Smolblog\ActivityPub\InboxService;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Framework\Objects\Identifier;

/**
 * ActivityPub Inbox endpoint.
 */
class SiteInbox extends BasicEndpoint {
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
			bodyClass: ActivityObject::class,
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
	 * Execute the endpoint.
	 *
	 * @throws BadRequest When the object does not match the site.
	 *
	 * @param Identifier|null $userId User making the request. Ignored.
	 * @param array|null      $params Site expected.
	 * @param object|null     $body   Some ActivityPub object expected.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): SuccessResponse {
		if (function_exists('wp_insert_post')) {
			wp_insert_post([
				'post_author' => 1,
				'post_content' => '<pre>' . print_r($body, true) . '</pre>',
				'post_title' => 'Hit on inbox ' . $params['site'],
			]);
		}

		switch (get_class($body)) {
			case Follow::class:
				if (is_string($body->object) && !str_contains($body->object, $params['site'])) {
					throw new BadRequest('Request sent to site inbox that does not target site.');
				}

				$this->service->handleFollow(request: $body, siteId: $params['site']);
				break;

			default:
				break;
		}

		return new SuccessResponse();
	}
}
