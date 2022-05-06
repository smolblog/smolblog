<?php

namespace Smolblog\Core\Definitions;

interface HttpResponse {
	/**
	 * HTTP response code for this response
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
	 *
	 * @return integer Status code for this response
	 */
	public function statusCode(): int;

	/**
	 * Headers to use in the response
	 *
	 * @return array
	 */
	public function headers(): array;

	/**
	 * Body of the response
	 *
	 * @return string
	 */
	public function body(): string;
}
