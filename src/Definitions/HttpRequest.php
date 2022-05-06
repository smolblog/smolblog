<?php

namespace Smolblog\Core\Definitions;

interface HttpRequest {
	/**
	 * HTTP verb used in this request.
	 *
	 * @return HttpVerb
	 */
	public function verb(): HttpVerb;

	/**
	 * Full URL of the request, including query string.
	 *
	 * @return string
	 */
	public function url(): string;

	/**
	 * Headers given in the request as an associative array.
	 *
	 * @return array
	 */
	public function headers(): array;

	/**
	 * Unparsed body of the request.
	 *
	 * @return string
	 */
	public function body(): string;
}
