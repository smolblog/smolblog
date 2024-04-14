<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Core\ContentV1\GenericContent;
use Smolblog\Core\ContentV1\Media\MediaList;
use Smolblog\Core\ContentV1\Queries\ContentList;
use Smolblog\Framework\Exceptions\InvalidMessageAttributesException;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get a list of recent content.
 */
class ListMedia extends BasicEndpoint {
	/**
	 * Get the endpoint's configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/media',
			pathVariables: [
				'site' => ParameterType::identifier(),
			],
			queryVariables: [
				'page' => ParameterType::integer(),
				'pageSize' => ParameterType::integer(),
				'type' => ParameterType::string(),
			],
			responseShape: ParameterType::object(
				count: ParameterType::integer(),
				media: ParameterType::required(ParameterType::array(
					items: ParameterType::fromClass(Media::class)
				))
			),
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
			'type' => $params['type'] ?? null,
		];

		if (is_string($opts['type'])) {
			$opts['type'] = [$opts['type']];
		}

		$query = new MediaList(...array_filter($opts), siteId: $params['site']);
		$this->bus->dispatch($query);

		try {
			return new GenericResponse(
				count: $query->count,
				content: $query->results(),
			);
		} catch (InvalidMessageAttributesException $e) {
			throw new BadRequest(previous: $e);
		}
	}
}
