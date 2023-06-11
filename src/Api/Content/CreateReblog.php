<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\Content\Types\Reblog\CreateReblog as ReblogCreateReblog;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Endpoint to create a minimal reblog post.
 */
class CreateReblog implements Endpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/reblog/new',
			verb: Verb::POST,
			pathVariables: [
				'site' => ParameterType::identifier(),
			],
			bodyClass: CreateReblogPayload::class,
			requiredScopes: [AuthScope::Write]
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus $bus MessageBus for sending the command.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId Required; user making the change.
	 * @param array|null      $params Expectes site parameter.
	 * @param object|null     $body   Instance of CreateReblogPayload.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): SuccessResponse {
		$this->bus->dispatch(new ReblogCreateReblog(
			url: $body->reblog->url,
			userId: $userId,
			siteId: $params['site'],
			publish: $body->publish,
			comment: $body->reblog->comment ?? null,
		));

		return new SuccessResponse();
	}
}
