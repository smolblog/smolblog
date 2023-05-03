<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\Content\Commands\ChangeContentVisibility;
use Smolblog\Core\Content\Commands\EditContentBaseAttributes;
use Smolblog\Core\Content\Extensions\SyndicationLinks\AddSyndicationLink;
use Smolblog\Core\Content\Extensions\Tags\SetTags;
use Smolblog\Core\Content\Types\Reblog\CreateReblog as ReblogCreateReblog;
use Smolblog\Core\Content\Types\Reblog\EditReblogComment;
use Smolblog\Core\Content\Types\Reblog\EditReblogUrl;
use Smolblog\Core\Content\Types\Status\EditStatus;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to update a reblog post.
 */
class UpdateStatus implements Endpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/status/{content}/update',
			verb: Verb::PUT,
			pathVariables: [
				'site' => ParameterType::identifier(),
				'content' => ParameterType::identifier(),
			],
			bodyClass: UpdateStatusPayload::class,
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
	 * @throws BadRequest Empty or incorrect body given.
	 *
	 * @param Identifier|null $userId Required; user making the change.
	 * @param array|null      $params Expectes site and content parameters.
	 * @param object|null     $body   Instance of UpdateStatusPayload.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): SuccessResponse {
		$standardCommandProps = [
			'userId' => $userId,
			'siteId' => $params['site'],
			'contentId' => $params['content'],
		];

		if (isset($body->text)) {
			$this->bus->dispatch(new EditStatus(
				siteId: $params['site'],
				userId: $userId,
				statusId: $params['content'],
				text: $body->text,
			));
		}
		if (isset($body->baseAttributes)) {
			$this->bus->dispatch(new EditContentBaseAttributes(
				...$standardCommandProps,
				permalink: $body->baseAttributes->permalink,
				publishTimestamp: $body->baseAttributes->publishTimestamp,
				authorId: $body->baseAttributes->authorId,
			));
		}
		if (isset($body->tags)) {
			$this->bus->dispatch(new SetTags(
				...$standardCommandProps,
				tags: $body->tags->tags,
			));
		}
		if (isset($body->syndicationLinks)) {
			foreach ($body->syndicationLinks->add as $link) {
				$this->bus->dispatch(new AddSyndicationLink(
					...$standardCommandProps,
					url: $link,
				));
			}
		}
		if (isset($body->visibility)) {
			$this->bus->dispatch(new ChangeContentVisibility(
				...$standardCommandProps,
				visibility: $body->visibility,
			));
		}

		return new SuccessResponse();
	}
}
