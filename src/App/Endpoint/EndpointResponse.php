<?php

namespace Smolblog\App\Endpoint;

use Smolblog\Framework\Value;

/**
 * Object to store a response for an Endpoint.
 */
readonly class EndpointResponse extends Value {
	/**
	 * HTTP response code for this response
	 *
	 * @var integer Status code for this response
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
	 */
	public int $statusCode;

	/**
	 * Body of the response to be converted to JSON
	 *
	 * @var array
	 */
	public array $body;

	/**
	 * Create the EndpointResponse
	 *
	 * @param array   $body       Body of the response.
	 * @param integer $statusCode HTTP code for the response; default 200 (OK).
	 */
	public function __construct(array $body, int $statusCode = 200) {
		$this->body = $body;
		$this->statusCode = $statusCode;
	}
}
