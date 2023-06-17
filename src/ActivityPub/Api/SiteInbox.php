<?php

namespace Smolblog\ActivityPub\Api;

use Smolblog\ActivityPhp\Type\Extended\Activity\Follow;
use Smolblog\ActivityPub\InboxService;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
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
	)
	{

	}

	public function run(?Identifier $userId, ?array $params, ?object $body): mixed
	{
		switch (get_class($body)) {
			case Follow::class:
				$this->service->handleFollow($body);
				break;

			default:
				# code...
				break;
		}
	}
}
