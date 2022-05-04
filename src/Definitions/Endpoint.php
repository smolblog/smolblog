<?php

namespace Smolblog\Core\Definitions;

use Smolblog\Core\Definitions\HttpVerb;
use Smolblog\Core\Definitions\SecurityLevel;

/**
 * Interface for declaring a REST API endpoint.
 */
interface Endpoint {
	/**
	 * The given route for this endpoint. If the endpoint is
	 * `smolblog.com/api/blog/info`, then this function should return
	 * `/blog/info` or `blog/info` (the opening slash will be inferred).
	 *
	 * To declare URL parameters, like `/blog/57/info`, use a regular expression:
	 * `/blog/(?P<id>[0-9]+)/info`.
	 *
	 * @return string Route for this Endpoint.
	 */
	public function route(): string;

	/**
	 * HTTP verbs this endpoint will respond to. Given as an array of HttpVerb
	 * enum instances.
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
	 * @see Smolblog\Core\Definitions\HttpVerb
	 *
	 * @return HttpVerb[]
	 */
	public function verbs(): array;

	public function security(): SecurityLevel;

	public function params(): array;

	public function run(array $requestInfo): array;
}
