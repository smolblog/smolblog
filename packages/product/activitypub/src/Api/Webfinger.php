<?php

namespace Smolblog\ActivityPub\Api;

use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Federation\SiteByResourceUri;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Find a user with Webfinger
 *
 * Implementation of the [Webfinger] standard. This protocol is used by Mastodon to enable user lookups from its
 * `@user@server` mention format. Smolblog considers interactions to originate from a site, so this will attempt to
 * find the site described.
 *
 * [Webfinger]: https://webfinger.net
 */
class Webfinger extends BasicEndpoint {
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
	 * Run the endpoint.
	 *
	 * @throws BadRequest When no resource is provided.
	 * @throws NotFound When the resource does not exist here.
	 *
	 * @param Identifier  $userId Ignored.
	 * @param array|null  $params Expects 'resource'.
	 * @param object|null $body   Ignored.
	 * @return WebfingerResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body = null): WebfingerResponse {
		if (empty($params['resource'])) {
			throw new BadRequest('No resource was provided.');
		}

		$site = $this->bus->fetch(new SiteByResourceUri($params['resource']));

		if (!isset($site)) {
			throw new NotFound('Could not find resource.');
		}

		return new WebfingerResponse(
			subject: $params['resource'],
			links: [
				new WebfingerLink(
					rel: 'http://webfinger.net/rel/profile-page',
					type: 'text/html',
					href: $site->baseUrl,
				),
				new WebfingerLink(
					rel: 'self',
					type: 'application/activity+json',
					href: $this->env->getApiUrl("/site/$site->id/activitypub/actor"),
				)
			]
		);
	}
}
