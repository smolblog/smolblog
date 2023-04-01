<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Content\Queries\GenericContentById;
use Smolblog\Core\Content\Types\Status\Status;
use Smolblog\Core\Content\Types\Status\StatusById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Get a particular Status content
 */
class GetStatus implements Endpoint {
	/**
	 * Get the endpoint's configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/content/status/{id}',
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
	 * @throws BadRequest Given ID is not a Status.
	 * @throws NotFound Given ID is not a visible piece of content.
	 *
	 * @param Identifier|null $userId User making the request.
	 * @param array|null      $params Expects id parameter from path.
	 * @param object|null     $body   Ignored.
	 * @return Status
	 */
	public function run(?Identifier $userId = null, ?array $params = [], ?object $body = null): Status {
		if (!$this->bus->fetch(new GenericContentById(id: $params['id'], userId: $userId))) {
			throw new NotFound('No content exists with that ID.');
		}

		$status = $this->bus->fetch(new StatusById(id: $params['id'], userId: $userId));
		if (!$status) {
			throw new BadRequest('Content is not a Status.');
		}

		return $status;
	}
}
