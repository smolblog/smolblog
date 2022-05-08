<?php

namespace Smolblog\Core\Definitions;

use JsonSerializable;

interface EndpointResponse {
	/**
	 * HTTP response code for this response
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
	 *
	 * @return integer Status code for this response
	 */
	public function statusCode(): int;

	/**
	 * Body of the response to be converted to JSON
	 *
	 * @return string
	 */
	public function body(): JsonSerializable|array;
}
