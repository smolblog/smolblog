<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Content\GenericContent;
use Smolblog\Core\Content\Queries\ContentList;
use Smolblog\Framework\Exceptions\InvalidMessageAttributesException;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Get a list of recent content.
 */
class ListContent implements Endpoint {
	/**
	 * Get the endpoint's configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content',
			queryVariables: [
				'page' => ParameterType::integer(),
				'pageSize' => ParameterType::integer(),
				'visibility' => ParameterType::string(),
				'type' => ParameterType::string(),
			],
			responseShape: ParameterType::object(
				content: ParameterType::required(ParameterType::array(
					items: ParameterType::fromClass(GenericContent::class)
				))
			),
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
	 * @throws BadRequest Page or PageSize is negative.
	 *
	 * @param Identifier|null $userId Required; user making the request.
	 * @param array|null      $params Any given filters.
	 * @param object|null     $body   Ignored.
	 * @return GenericResponse
	 */
	public function run(?Identifier $userId = null, ?array $params = [], ?object $body = null): GenericResponse {
		$opts = [
			'page' => $params['page'] ?? null,
			'pageSize' => $params['pageSize'] ?? null,
			'userId' => $userId ?? null,
			'visibility' => $params['visibility'] ?? null,
			'types' => $params['types'] ?? null,
		];

		if (is_string($opts['visibility'])) {
			$opts['visibility'] = [$opts['visibility']];
		}
		if (is_string($opts['types'])) {
			$opts['types'] = [$opts['types']];
		}

		try {
			return new GenericResponse(
				content: $this->bus->fetch(new ContentList(...array_filter($opts), siteId: $params['site']))
			);
		} catch (InvalidMessageAttributesException $e) {
			throw new BadRequest(previous: $e);
		}
	}
}
