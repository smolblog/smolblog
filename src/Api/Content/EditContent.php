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
use Smolblog\Framework\Objects\DateIdentifier;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to create a minimal reblog post.
 */
class EditContent extends BasicEndpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/{id}/edit',
			verb: Verb::POST,
			pathVariables: [
				'site' => ParameterType::identifier(),
				'id' => ParameterType::identifier(),
			],
			bodyClass: ContentPayload::class,
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
	 * @throws BadRequest When a valid content type is not given.
	 * @throws NotFound When the ID does not match any editable content.
	 *
	 * @param Identifier|null $userId Required; user making the change.
	 * @param array|null      $params Expectes site and id parameters.
	 * @param object|null     $body   Instance of ContentPayload.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): SuccessResponse {
		$query = new ContentById(
			id: $params['id'],
			userId: $userId,
			siteId: $params['site'],
		);
		$content = $this->bus->fetch($query);

		if (!$content) {
			throw new NotFound("No editable content exists with that ID.");
		}

		$contentParams = [
			'contentId' => $params['id'],
			'siteId' => $params['site'],
			'userId' => $userId,
		];

		if (isset($body->type)) {
			switch ($body->type->type) {
				case 'note':
					$this->bus->dispatch(new EditNote(...$contentParams, text: $body->type->text));
					break;

				case 'reblog':
					if ($body->type->url) {
						$this->bus->dispatch(new EditReblogUrl(
							...$contentParams,
							url: $body->type->url,
						));
					}
					if ($body->type->comment) {
						$this->bus->dispatch(new EditReblogComment(
							...$contentParams,
							comment: $body->type->comment,
						));
					}
					break;

				case 'picture':
					if ($body->type->mediaIds) {
						$this->bus->dispatch(new EditPictureMedia(
							...$contentParams,
							mediaIds: $body->type->mediaIds,
						));
					}
					if ($body->type->caption) {
						$this->bus->dispatch(new EditPictureCaption(
							...$contentParams,
							caption: $body->type->caption,
						));
					}
					break;

				default:
					throw new BadRequest("Invalid content type $body->type->type");
					break;
			}//end switch
		}//end if

		if (!empty($body->meta)) {
			$this->bus->dispatch(new EditContentBaseAttributes(
				...$contentParams,
				publishTimestamp: $body->meta->publishTimestamp,
				authorId: $body->meta->authorId,
			));
		}

		if (isset($body->extensions['tags'])) {
			$this->bus->dispatch(new SetTags(
				...$contentParams,
				tags: $body->extensions['tags']['tags'],
			));
		}

		if (isset($body->extensions['syndication'])) {
			if ($body->extensions['syndication']['channels']) {
				$this->bus->dispatch(new SetSyndicationChannels(
					...$contentParams,
					channels: $body->extensions['syndication']['channels'],
				));
			}

			if ($body->extensions['syndication']['links']) {
				$existing = array_map(fn($link) => $link->url, $content->extensions[Syndication::class]->links);

				foreach ($body->extensions['syndication']['links'] as $url) {
					if (!in_array($url, $existing)) {
						$this->bus->dispatch(new AddSyndicationLink(
							...$contentParams,
							url: $url,
						));
					}
				}
			}
		}//end if

		if ($content->visibility !== ContentVisibility::Published && $body->published) {
			$this->bus->dispatch(match ($body->type->type) {
				'note' => new PublishNote(...$contentParams),
				'reblog' => new PublishReblog(...$contentParams),
				'picture' => new PublishPicture(...$contentParams),
			});
		}

		return new SuccessResponse();
	}
}
