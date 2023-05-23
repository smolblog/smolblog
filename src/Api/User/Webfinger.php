<?php

namespace Smolblog\Api\User;

use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Find a user with Webfinger
 *
 * Implementation of the [Webfinger] standard. This protocol is used by Mastodon to enable user lookups from its
 * `@user@server` mention format.
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
			responseShape: ParameterType::object(
				subject: ParameterType::string(),
				links: ParameterType::array(ParameterType::object(
					rel: ParameterType::string(),
					type: ParameterType::string(),
					href: ParameterType::string(),
				))
			),
			requiredScopes: [],
		);
	}

	public function run(?Identifier $userId, ?array $params, ?object $body): WebfingerResponse {
		return new WebfingerResponse(
			subject: $params['resource'],
		);
	}
}
