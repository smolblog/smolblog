<?php

namespace Smolblog\Api\Site;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Site\GetSiteSettings;
use Smolblog\Core\Site\SiteSettings;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to get a site's settings.
 */
class GetSettings implements Endpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/settings',
			pathVariables: [
				'site' => ParameterType::identifier(),
			],
			requiredScopes: [AuthScope::Read],
		);
	}

	/**
	 * Construct the endpoint
	 *
	 * @param MessageBus $bus MessageBus for queries.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier  $userId Required; currently logged in user.
	 * @param array       $params Expects site parameter.
	 * @param object|null $body   Ignored.
	 * @return SiteSettings
	 */
	public function run(?Identifier $userId = null, ?array $params = null, ?object $body = null): SiteSettings {
		return $this->bus->fetch(new GetSiteSettings(siteId: $params['site'], userId: $userId));
	}
}
