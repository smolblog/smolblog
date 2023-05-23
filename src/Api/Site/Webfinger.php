<?php

namespace Smolblog\Api\Site;

use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Framework\Objects\Identifier;

/**
 * Find a user with Webfinger
 *
 * Implementation of the [Webfinger] standard. This protocol is used by Mastodon to enable user lookups from its
 * `@user@server` mention format. Smolblog considers interactions to originate from a site, so this will attempt to
 * find the site described.
 *
 * [Webfinger]: https://webfinger.net
 */
class Webfinger implements Endpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/webfinger',
			queryVariables: [
				'resource' => ParameterType::required(ParameterType::string()),
				'rel' => ParameterType::string(),
			],
			requiredScopes: [],
		);
	}

	/**
	 * Run the endpoint.
	 *
	 * @param Identifier|null $userId Ignored.
	 * @param array|null      $params Expects 'resource'.
	 * @param object|null     $body   Ignored.
	 * @return WebfingerResponse
	 */
	public function run(?Identifier $userId = null, ?array $params, ?object $body = null): WebfingerResponse {
		return new WebfingerResponse(
			subject: $params['resource'],
		);
	}
}
