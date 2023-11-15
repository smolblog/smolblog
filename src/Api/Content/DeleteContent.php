<?php

namespace Smolblog\Api\Content;

use Exception;
use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\Content\ContentTypeRegistry;
use Smolblog\Core\Content\Media\DeleteMedia as DeleteMediaCommand;
use Smolblog\Core\Content\Media\MediaById;
use Smolblog\Core\Content\Queries\ContentById;
use Smolblog\Core\Content\Queries\GenericContentById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to delete media.
 */
class DeleteContent extends BasicEndpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/{id}/delete',
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
	 * @param MessageBus          $bus     MessageBus for sending the command.
	 * @param ContentTypeRegistry $typeReg Available Content Types.
	 */
	public function __construct(
		private MessageBus $bus,
		private ContentTypeRegistry $typeReg,
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @throws NotFound When the ID does not match any editable media.
	 * @throws Exception When there is no delete command.
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

		$query = new ContentById(...$contentParams);
		$content = $this->bus->fetch($query);

		if (!$content) {
			throw new NotFound("No editable content exists with that ID.");
		}

		$commandClass = $this->typeReg->deleteItemCommandFor($content->type->getTypeKey());
		if (!isset($commandClass) || !class_exists($commandClass)) {
			throw new Exception("No delete command exists for this content type.");
		}

		$this->bus->dispatch(new $commandClass(...$contentParams));

		return new SuccessResponse();
	}
}
