<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Content\Queries\GenericContentById;
use Smolblog\Core\Content\Types\Reblog\Reblog;
use Smolblog\Core\Content\Types\Reblog\ReblogById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Get a particular Reblog content
 */
class GetReblog implements Endpoint {
	/**
	 * Get the endpoint's configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/content/reblog/{id}',
			pathVariables: ['id' => ParameterType::identifier()],
			requiredScopes: [AuthScope::Read],
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
	 * @throws BadRequest Given ID is not a Reblog.
	 * @throws NotFound Given ID is not a visible piece of content.
	 *
	 * @param Identifier|null $userId User making the request.
	 * @param array|null      $params Expects id parameter from path.
	 * @param object|null     $body   Ignored.
	 * @return Reblog
	 */
	public function run(?Identifier $userId = null, ?array $params = [], ?object $body = null): Reblog {
		if (!$this->bus->fetch(new GenericContentById(id: $params['id'], userId: $userId))) {
			throw new NotFound('No content exists with that ID.');
		}

		$reblog = $this->bus->fetch(new ReblogById(id: $params['id'], userId: $userId));
		if (!$reblog) {
			throw new BadRequest('Content is not a Reblog.');
		}

		return $reblog;
	}
}
