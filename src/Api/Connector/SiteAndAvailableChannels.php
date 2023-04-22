<?php

namespace Smolblog\Api\Connector;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Connector\Queries\ChannelsForAdmin;
use Smolblog\Core\Connector\Queries\ChannelsForSite;
use Smolblog\Core\Connector\Queries\ConnectionsForUser;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Get both existing and available channels along with basic connection info.
 *
 * This endpoint is intended for the admin screen.
 */
class SiteAndAvailableChannels implements Endpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/channels',
			pathVariables: ['site' => ParameterType::identifier()],
			responseShape: ParameterType::object(
				channels: ParameterType::required(ParameterType::array(
					items: ParameterType::fromClass(Channel::class),
				))
			),
			requiredScopes: [AuthScope::Read],
		);
	}

	/**
	 * Construct the endpoint;
	 *
	 * @param MessageBus $bus For sending queries.
	 */
	public function __construct(private MessageBus $bus) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId Expected.
	 * @param array|null      $params Expects site.
	 * @param object|null     $body   Ignored.
	 * @return GenericResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body = null): GenericResponse {
		$results = $this->bus->fetch(new ChannelsForAdmin(siteId: $params['site'], userId: $userId));
		return new GenericResponse(not: 'implemented');
	}
}
