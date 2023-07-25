<?php

namespace Smolblog\Api\Site;

use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\Site\UpdateSettings as UpdateSettingsCommand;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Update site-specific settings.
 */
class UpdateSettings extends BasicEndpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/settings/set',
			verb: Verb::PUT,
			pathVariables: [
				'site' => ParameterType::identifier(),
			],
			bodyClass: SiteSettingsPayload::class,
			requiredScopes: [AuthScope::Admin],
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
	public function run(?Identifier $userId = null, ?array $params = null, ?object $body = null): SuccessResponse {
		$this->bus->dispatch(new UpdateSettingsCommand(
			siteId: $params['site'],
			userId: $userId,
			siteName: $body->title,
			siteTagline: $body->tagline,
		));

		return new SuccessResponse();
	}
}
