<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\Content\Commands\EditContentBaseAttributes;
use Smolblog\Core\Content\Extensions\Syndication\AddSyndicationLink;
use Smolblog\Core\Content\Extensions\Syndication\SetSyndicationChannels;
use Smolblog\Core\Content\Extensions\Syndication\SyndicationService;
use Smolblog\Core\Content\Extensions\Tags\SetTags;
use Smolblog\Core\Content\Types\Note\CreateNote;
use Smolblog\Core\Content\Types\Note\PublishNote;
use Smolblog\Core\Content\Types\Picture\CreatePicture;
use Smolblog\Core\Content\Types\Picture\PublishPicture;
use Smolblog\Core\Content\Types\Reblog\CreateReblog;
use Smolblog\Core\Content\Types\Reblog\PublishReblog;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DateIdentifier;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to create a minimal reblog post.
 */
class NewContent extends BasicEndpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/new',
			verb: Verb::POST,
			pathVariables: [
				'site' => ParameterType::identifier(),
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
	 *
	 * @param Identifier|null $userId Required; user making the change.
	 * @param array|null      $params Expectes site parameter.
	 * @param object|null     $body   Instance of ContentPayload.
	 * @return GenericResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): GenericResponse {
		$id = $body->id ?? new DateIdentifier();

		$contentParams = [
			'contentId' => $id,
			'siteId' => Identifier::fromString($params['site']),
			'userId' => $userId,
		];

		switch ($body->type->type) {
			case 'note':
				$this->bus->dispatch(new CreateNote(...$contentParams, text: $body->type->text));
				break;

			case 'reblog':
				$this->bus->dispatch(new CreateReblog(
					...$contentParams,
					url: $body->type->url,
					comment: $body->type->comment,
				));
				break;

			case 'picture':
				$this->bus->dispatch(new CreatePicture(
					...$contentParams,
					mediaIds: $body->type->mediaIds,
					caption: $body->type->caption,
				));
				break;

			default:
				throw new BadRequest("Invalid content type $body->type->type");
				break;
		}//end switch

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
				tags: $body->extensions['tags']->tags,
			));
		}

		if (isset($body->extensions['syndication'])) {
			$this->bus->dispatch(new SetSyndicationChannels(
				...$contentParams,
				channels: $body->extensions['syndication']->channels,
			));

			foreach ($body->extensions['syndication']->links as $url) {
				$this->bus->dispatch(new AddSyndicationLink(
					...$contentParams,
					url: $url,
				));
			}
		}

		if ($body->publishNow) {
			$this->bus->dispatch(match ($body->type->type) {
				'note' => new PublishNote(...$contentParams),
				'reblog' => new PublishReblog(...$contentParams),
				'picture' => new PublishPicture(...$contentParams),
			});
		}

		return new GenericResponse(id: $id);
	}
}
