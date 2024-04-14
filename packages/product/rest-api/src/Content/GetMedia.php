<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Core\ContentV1\Media\Media;
use Smolblog\Core\ContentV1\Media\MediaById;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get a specific piece of content.
 */
class GetMedia extends BasicEndpoint {
	/**
	 * Get the endpoint's configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/media/{id}',
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
	 * @return Media
	 */
	public function run(?Identifier $userId = null, ?array $params = [], ?object $body = null): Media {
		$query = new MediaById(
			contentId: $params['id'],
			userId: $userId,
			siteId: $params['site'],
		);
		$content = $this->bus->fetch($query);

		if (!isset($content)) {
			throw new NotFound('No media found with ID ' . $params['id']);
		}

		return $content;
	}
}
