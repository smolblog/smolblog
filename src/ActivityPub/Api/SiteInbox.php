<?php

namespace Smolblog\ActivityPub\Api;

use Smolblog\ActivityPhp\Type\Extended\Activity\Follow;
use Smolblog\ActivityPub\InboxService;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Framework\Objects\Identifier;

class SiteInbox implements Endpoint {
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/activitypub/inbox',
			verb: Verb::POST,
			pathVariables: ['site' => ParameterType::identifier()],
			bodyClass: ActivityObject::class,
			requiredScopes: [],
		);
	}

	public function __construct(
		private InboxService $service,
	) {
	}

	public function run(?Identifier $userId, ?array $params, ?object $body): SuccessResponse {
		switch (get_class($body)) {
			case Follow::class:
				if (is_string($body->object) && !str_contains($body->object, $params['site'])) {
					throw new BadRequest('Request sent to site inbox that does not target site.');
				}

				$this->service->handleFollow(request: $body, siteId: $params['site']);
				break;

			default:
				if (function_exists('wp_insert_post')) {
					wp_insert_post([
						'post_author' => 1,
						'post_content' => '<pre>' . print_r($body, true) . '</pre>',
						'post_title' => 'Hit on inbox ' . $params['site'],
					]);
				}
				break;
		}

		return new SuccessResponse();
	}
}
