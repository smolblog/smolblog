<?php

namespace Smolblog\Core;

// This sniff apparently does not support `readonly`.
//phpcs:disable Squiz.Commenting.VariableComment.Missing

use Smolblog\Core\Definitions\HttpVerb;

/**
 * Represents a request made to an endpoint.
 */
class EndpointRequest {
	/**
	 * Context of the request (user and site)
	 *
	 * @var RequestContext
	 */
	public readonly RequestContext $context;

	/**
	 * Parsed params as defined by the Endpoint
	 *
	 * @var array
	 */
	public readonly array $params;

	/**
	 * Body of the request as an associative array; false if the
	 * body is not parsable JSON or is empty.
	 *
	 * @var array|false
	 */
	public readonly array | false $json;

	/**
	 * Build the EndpointRequest
	 *
	 * @param RequestContext $context Context for the request.
	 * @param array          $params  Parsed params as defined by the Endpoint.
	 * @param array|false    $json    Body of the request as an associative array (false if empty).
	 */
	public function __construct(
		RequestContext $context,
		array $params = [],
		array|false $json = false
	) {
		$this->context = $context;
		$this->params = $params;
		$this->json = $json;
	}
}
