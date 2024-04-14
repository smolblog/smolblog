<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\ContentV1\Commands\EditContentBaseAttributes;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Core\ContentV1\Extensions\Syndication\AddSyndicationLink;
use Smolblog\Core\ContentV1\Extensions\Syndication\SetSyndicationChannels;
use Smolblog\Core\ContentV1\Extensions\Syndication\Syndication;
use Smolblog\Core\ContentV1\Extensions\Syndication\SyndicationService;
use Smolblog\Core\ContentV1\Extensions\Tags\SetTags;
use Smolblog\Core\ContentV1\Media\EditMediaAttributes;
use Smolblog\Core\ContentV1\Media\MediaById;
use Smolblog\Core\ContentV1\Queries\ContentById;
use Smolblog\Core\ContentV1\Types\Note\CreateNote;
use Smolblog\Core\ContentV1\Types\Note\EditNote;
use Smolblog\Core\ContentV1\Types\Note\PublishNote;
use Smolblog\Core\ContentV1\Types\Picture\CreatePicture;
use Smolblog\Core\ContentV1\Types\Picture\EditPictureCaption;
use Smolblog\Core\ContentV1\Types\Picture\EditPictureMedia;
use Smolblog\Core\ContentV1\Types\Picture\PublishPicture;
use Smolblog\Core\ContentV1\Types\Reblog\CreateReblog;
use Smolblog\Core\ContentV1\Types\Reblog\EditReblogComment;
use Smolblog\Core\ContentV1\Types\Reblog\EditReblogUrl;
use Smolblog\Core\ContentV1\Types\Reblog\PublishReblog;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Endpoint to create a minimal reblog post.
 */
class EditMedia extends BasicEndpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/media/{id}/edit',
			verb: Verb::POST,
			pathVariables: [
				'site' => ParameterType::identifier(),
				'id' => ParameterType::identifier(),
			],
			bodyClass: EditMediaPayload::class,
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

		$this->bus->dispatch(new EditMediaAttributes(
			...$contentParams,
			title: $body->title,
			accessibilityText: $body->accessibilityText,
		));

		return new SuccessResponse();
	}
}
