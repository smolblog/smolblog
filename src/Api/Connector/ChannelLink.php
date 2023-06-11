<?php

namespace Smolblog\Api\Connector;

use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\Connector\Commands\LinkChannelToSite;
use Smolblog\Core\Connector\Queries\ChannelById;
use Smolblog\Core\Site\SiteById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Set permissions for a Channel and Site.
 *
 * Requires administrator permission for the site and ownership of the Connector that provides the Channel. Set `push`
 * to allow the site to send content into the Channel. Set `pull` to allow the site to retrieve content from the
 * Channel.
 */
class ChannelLink implements Endpoint {
	/**
	 * Get the configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: 'connect/link',
			verb: Verb::POST,
			bodyClass: ChannelLinkRequest::class,
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus $bus MessageBus for queries and commands.
	 */
	public function __construct(
		private MessageBus $bus,
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @throws BadRequest Channel or Site not found.
	 *
	 * @param Identifier|null $userId ID of the user making the request.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Expected to be ChannelLinkRequest.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId = null, ?array $params = null, ?object $body = null): SuccessResponse {
		if (
			$this->bus->fetch(new ChannelById($body->channelId)) === null ||
			$this->bus->fetch(new SiteById($body->siteId)) === null
		) {
			throw new BadRequest("Site or channel not found.");
		}

		$this->bus->dispatch(new LinkChannelToSite(
			channelId: $body->channelId,
			siteId: $body->siteId,
			userId: $userId,
			canPush: $body->push ?? false,
			canPull: $body->pull ?? false,
		));

		return new SuccessResponse();
	}
}
