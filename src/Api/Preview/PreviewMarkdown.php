<?php

namespace Smolblog\Api\Preview;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\GenericResponse;
use Smolblog\Api\ParameterType;
use Smolblog\Api\Verb;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Get some Smolblog-flavored Markdown and render it server-side.
 *
 * This is particularly so client applications can get a "canonical" rendering if it is important. And so we don't have
 * to re-write all of the Smolblog-specific Markdown idioms in JavaScript. At least not for now.
 *
 * Even though there are no side-effects and this could safely be a GET request, the longer nature of some markdown
 * fields could exceed the request size for a GET request. So this is a POST request instead.
 */
class PreviewMarkdown implements Endpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/preview/markdown',
			verb: Verb::POST,
			bodyClass: PreviewMarkdownBody::class,
			responseShape: ParameterType::object(
				rendered: ParameterType::string()
			),
			requiredScopes: [AuthScope::Identified]
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param SmolblogMarkdown $md Markdown parser.
	 */
	public function __construct(
		private SmolblogMarkdown $md
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId Ignored.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Expected to have a $sfmd property.
	 * @return GenericResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): GenericResponse {
		return new GenericResponse(rendered: $this->md->parse($body->sfmd));
	}
}
