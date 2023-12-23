<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\Content\Media\DeleteMedia as DeleteMediaCommand;
use Smolblog\Core\Content\Media\MediaById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to delete media.
 */
class DeleteMedia extends BasicEndpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/media/{id}/delete',
			verb: Verb::DELETE,
			pathVariables: [
				'site' => ParameterType::identifier(),
				'id' => ParameterType::identifier(),
			],
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
	 * @throws NotFound When the ID does not match any editable media.
	 *
	 * @param Identifier|null $userId Required; user making the change.
	 * @param array|null      $params Expectes site and id parameters.
	 * @param object|null     $body   Instance of EditMediaPayload.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): SuccessResponse {
		$contentParams = [
			'contentId' => $params['id'],
			'siteId' => $params['site'],
			'userId' => $userId,
		];

		$query = new MediaById(...$contentParams);
		$content = $this->bus->fetch($query);

		if (!$content) {
			throw new NotFound("No editable media exists with that ID.");
		}

		$this->bus->dispatch(new DeleteMediaCommand(
			...$contentParams,
		));

		return new SuccessResponse();
	}
}
