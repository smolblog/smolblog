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
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to update a reblog post.
 */
class UpdateReblog implements Endpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/reblog/{content}/update',
			verb: Verb::PUT,
			pathVariables: [
				'site' => ParameterType::identifier(),
				'content' => ParameterType::identifier(),
			],
			bodyClass: UpdateReblogPayload::class,
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
	 * @param object|null     $body   Instance of UpdateReblogPayload.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): SuccessResponse {
		$standardCommandProps = [
			'userId' => $userId,
			'siteId' => $params['site'],
			'contentId' => $params['content'],
		];

		if (isset($body->reblog)) {
			$this->handleReblog($body->reblog, [
				'userId' => $userId,
				'siteId' => $params['site'],
				'reblogId' => $params['content'],
			]);
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

	/**
	 * Handle the reblog payload.
	 *
	 * @throws BadRequest If neither URL or Comment are provided.
	 *
	 * @param BaseReblogPayload $reblog Reblog payload to handle.
	 * @param array             $props  Standard command props.
	 * @return void
	 */
	private function handleReblog(BaseReblogPayload $reblog, array $props) {
		if (!isset($reblog->url) && !isset($reblog->comment)) {
			throw new BadRequest('Reblog provided with no updated attributes.');
		}

		if (isset($reblog->url)) {
			$this->bus->dispatch(new EditReblogUrl(
				...$props,
				url: $reblog->url,
			));
		}
		if (isset($reblog->comment)) {
			$this->bus->dispatch(new EditReblogComment(
				...$props,
				comment: $reblog->comment,
			));
		}
	}
}
