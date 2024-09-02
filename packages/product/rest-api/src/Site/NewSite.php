<?php

namespace Smolblog\Api\Site;

use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Api\Verb;
use Smolblog\Core\Site\CreateSite;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Create a new site.
 */
class NewSite extends BasicEndpoint {
	/**
	 * Get the configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/new',
			verb: Verb::POST,
			bodyClass: NewSitePayload::class,
			requiredScopes: [AuthScope::Admin],
			responseShape: ParameterType::object(
				id: ParameterType::identifier()
			),
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus $bus For sending commands.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId User creating the site.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Instance of NewSitePayload.
	 * @return GenericResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): GenericResponse {
		$command = new CreateSite(
			userId: $userId,
			handle: $body->handle,
			displayName: $body->displayName,
			baseUrl: "https://$body->handle.smol.blog/", // Hard-coded for now.
		);

		$this->bus->dispatch($command);

		return new GenericResponse(id: $command->siteId);
	}
}
