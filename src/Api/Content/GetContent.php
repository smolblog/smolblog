<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Queries\ContentById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Get a specific piece of content.
 */
class GetContent extends BasicEndpoint {
	/**
	 * Get the endpoint's configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/{id}',
			pathVariables: [
				'site' => ParameterType::identifier(),
				'id' => ParameterType::identifier(),
			],
			requiredScopes: [AuthScope::Identified],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus $bus MessageBus to send queries.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @throws NotFound No post found with that ID.
	 *
	 * @param Identifier|null $userId Required; user making the request.
	 * @param array|null      $params Any given filters.
	 * @param object|null     $body   Ignored.
	 * @return ContentPayload
	 */
	public function run(?Identifier $userId = null, ?array $params = [], ?object $body = null): ContentPayload {
		$query = new ContentById(
			id: $params['id'],
			userId: $userId,
			siteId: $params['site'],
		);
		$content = $this->bus->fetch($query);

		if (!isset($content)) {
			throw new NotFound('No post found with ID ' . $params['id']);
		}

		return new ContentPayload(
			id: $content->id,
			type: new ContentTypePayload(
				...$content->type->toArray(),
				type: $content->type->getTypeKey(),
			),
			meta: new BaseAttributesPayload(
				permalink: $content->permalink,
				publishTimestamp: $content->publishTimestamp,
				authorId: $content->authorId,
			),
			extensions: $content->extensions,
			published: match ($content->visibility) {
				ContentVisibility::Published => true,
				default => false,
			}
		);
	}
}
