<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\Content\Types\Note\CreateNote as CreateNoteCommand;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to create a minimal reblog post.
 */
class CreateNote implements Endpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/note/new',
			verb: Verb::POST,
			pathVariables: [
				'site' => ParameterType::identifier(),
			],
			bodyClass: CreateNotePayload::class,
			requiredScopes: [AuthScope::Create]
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
	 * @param object|null     $body   Instance of CreateNotePayload.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): SuccessResponse {
		$this->bus->dispatch(new CreateNoteCommand(
			text: $body->text,
			userId: $userId,
			siteId: $params['site'],
			publish: $body->publish,
		));

		return new SuccessResponse();
	}
}
