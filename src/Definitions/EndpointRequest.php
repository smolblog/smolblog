<?php

namespace Smolblog\Definitions;

interface EndpointRequest extends HttpRequest {
	/**
	 * Parsed params as defined by the Endpoint
	 *
	 * @return array
	 */
	public function params(): array;

	/**
	 * Body of the request as an associative array. Should return false if the
	 * body is not parsable JSON.
	 *
	 * @return array|false
	 */
	public function json(): array|false;
}
