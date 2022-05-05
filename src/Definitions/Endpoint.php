<?php

namespace Smolblog\Core\Definitions;

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

	/**
	 * Security level for this endpoint. The user making the request will need to
	 * have permissions at or above this level or a 401 or 403 response will be
	 * given.
	 *
	 * @return SecurityLevel
	 */
	public function security(): SecurityLevel;

	/**
	 * Parameters for this endpoint in an array of EndpointParameters.
	 *
	 * Parameters given in this array will be proviced to run()
	 *
	 * @return EndpointParameter[]
	 */
	public function params(): array;

	/**
	 * Undocumented function
	 *
	 * @param array $requestInfo
	 * @return array
	 */
	public function run(HttpRequest $requestInfo): array;
}
