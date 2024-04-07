<?php

namespace Smolblog\Api\Preview;

use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\ParameterType;
use Smolblog\Core\ContentV1\Types\Reblog\ExternalContentInfo;
use Smolblog\Core\ContentV1\Types\Reblog\ExternalContentService;
use Smolblog\Framework\Objects\Identifier;

/**
 * Get embed info for an external URL.
 */
class PreviewEmbed extends BasicEndpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/preview/embed',
			queryVariables: [
				'url' => ParameterType::string(format: 'url'),
			],
			requiredScopes: [AuthScope::Identified],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param ExternalContentService $embed Service to get external URL info.
	 */
	public function __construct(
		private ExternalContentService $embed
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @throws BadRequest URL not provided or not valid.
	 *
	 * @param Identifier|null $userId Ignored.
	 * @param array|null      $params A $parameter is expected.
	 * @param object|null     $body   Ignored.
	 * @return ExternalContentInfo
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): ExternalContentInfo {
		if (filter_var($params['url'], FILTER_VALIDATE_URL) === false) {
			throw new BadRequest('URL was not provided or was not valid.');
		}

		return $this->embed->getExternalContentInfo($params['url']);
	}
}
