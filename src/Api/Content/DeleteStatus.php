<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\Content\Types\Status\DeleteStatus as DeleteStatusCommand;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to delete a status.
 */
class DeleteStatus implements Endpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/status/{content}/delete',
			verb: Verb::DELETE,
			pathVariables: [
				'site' => ParameterType::identifier(),
				'content' => ParameterType::identifier(),
			],
			requiredScopes: [AuthScope::Write]
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus $bus For dispatching commands.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId Required.
	 * @param array|null      $params Expects content and site from path.
	 * @param object|null     $body   Ignored.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): SuccessResponse {
		$this->bus->dispatch(new DeleteStatusCommand(
			siteId: $params['site'],
			userId: $userId,
			statusId: $params['content'],
		));

		return new SuccessResponse();
	}
}