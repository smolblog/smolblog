<?php

namespace Smolblog\Core\Endpoint;

// This sniff apparently does not support `readonly`.
//phpcs:disable Squiz.Commenting.VariableComment.Missing

use JsonSerializable;

/**
 * Object to store a response for an Endpoint.
 */
class EndpointResponse {
	/**
	 * HTTP response code for this response
	 *
	 * @var integer Status code for this response
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
	 */
	public readonly int $statusCode;

	/**
	 * Body of the response to be converted to JSON
	 *
	 * @var JsonSerializable|array
	 */
	public readonly JsonSerializable | array $body;

	/**
	 * Create the EndpointResponse
	 *
	 * @param JsonSerializable|array $body       Body of the response.
	 * @param integer                $statusCode HTTP code for the response; default 200 (OK).
	 */
	public function __construct(JsonSerializable | array $body, int $statusCode = 200) {
		$this->body = $body;
		$this->statusCode = $statusCode;
	}
}
