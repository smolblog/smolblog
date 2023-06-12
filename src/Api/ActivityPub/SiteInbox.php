<?php

namespace Smolblog\Api\ActivityPub;

use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Verb;

class SiteInbox implements Endpoint {
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/activitypub/inbox',
			verb: Verb::POST,
			pathVariables: ['site' => ParameterType::identifier()],
			requiredScopes: [],
		);
	}
}
