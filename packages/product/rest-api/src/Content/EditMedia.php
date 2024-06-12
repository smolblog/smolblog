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
use Smolblog\Core\Content\Commands\EditContentBaseAttributes;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Extensions\Syndication\AddSyndicationLink;
use Smolblog\Core\Content\Extensions\Syndication\SetSyndicationChannels;
use Smolblog\Core\Content\Extensions\Syndication\Syndication;
use Smolblog\Core\Content\Extensions\Syndication\SyndicationService;
use Smolblog\Core\Content\Extensions\Tags\SetTags;
use Smolblog\Core\Content\Media\EditMediaAttributes;
use Smolblog\Core\Content\Media\MediaById;
use Smolblog\Core\Content\Queries\ContentById;
use Smolblog\Core\Content\Types\Note\CreateNote;
use Smolblog\Core\Content\Types\Note\EditNote;
use Smolblog\Core\Content\Types\Note\PublishNote;
use Smolblog\Core\Content\Types\Picture\CreatePicture;
use Smolblog\Core\Content\Types\Picture\EditPictureCaption;
use Smolblog\Core\Content\Types\Picture\EditPictureMedia;
use Smolblog\Core\Content\Types\Picture\PublishPicture;
use Smolblog\Core\Content\Types\Reblog\CreateReblog;
use Smolblog\Core\Content\Types\Reblog\EditReblogComment;
use Smolblog\Core\Content\Types\Reblog\EditReblogUrl;
use Smolblog\Core\Content\Types\Reblog\PublishReblog;
use Smolblog\Framework\Messages\MessageBus;
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
