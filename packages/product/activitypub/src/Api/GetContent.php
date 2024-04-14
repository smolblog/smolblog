<?php

namespace Smolblog\ActivityPub\Api;

use Smolblog\ActivityPub\ActivityTypesConverter;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Core\ContentV1\Queries\ContentById;
use Smolblog\Core\Site\SiteById;
use Smolblog\Framework\ActivityPub\Objects\ActivityPubObject;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get the ActivityPub object for a particular piece of content.
 */
class GetContent extends BasicEndpoint {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/activitypub/content/{id}',
			pathVariables: [
				'site' => ParameterType::identifier(),
				'id' => ParameterType::identifier(),
			],
			requiredScopes: [],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus             $bus For sending messages.
	 * @param ActivityTypesConverter $at  For converting objects.
	 */
	public function __construct(
		private MessageBus $bus,
		private ActivityTypesConverter $at,
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @throws NotFound When either the site or content is not found.
	 *
	 * @param Identifier|null $userId Ignored.
	 * @param array|null      $params Expects site and id.
	 * @param object|null     $body   Ignored.
	 * @return ActivityPubObject
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): ActivityPubObject {
		$site = $this->bus->fetch(new SiteById($params['site']));
		if (!isset($site)) {
			throw new NotFound('No site was found with ID ' . $params['site']);
		}

		$content = $this->bus->fetch(new ContentById(id: $params['id'], siteId: $params['site']));
		if (!isset($content)) {
			throw new NotFound('No site was found with ID ' . $params['site']);
		}

		return $this->at->activityObjectFromContent(content: $content, site: $site);
	}
}
