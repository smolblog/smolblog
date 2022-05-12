<?php

namespace Smolblog\Core\Definitions;

interface EndpointRequest {
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

	/**
	 * Information about the current running environment (blog, user, etc)
	 *
	 * @return array
	 */
	public function environment(): array;
}
