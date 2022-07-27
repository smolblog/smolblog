<?php

namespace Smolblog\Core;

class EndpointConfig {
		/**
		 * The given route for this endpoint. If the endpoint is
		 * `smolblog.com/api/blog/info`, then this function should return
		 * `/blog/info` or `blog/info` (the opening slash will be inferred).
		 *
		 * To declare URL parameters, add the parameter's slug within brackets
		 * (e.g. `post/[id]/comments`).
		 *
		 * @var string
		 */
	public readonly string $route;

	/**
	 * HTTP verbs this endpoint will respond to. Given as an array of HttpVerb
	 * enum instances.
	 *
	 * @var HttpVerb[]
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
	 */
	public readonly array $verbs;

	/**
	 * Security level for this endpoint. The user making the request will need to
	 * have permissions at or above this level or a 401 or 403 response will be
	 * given.
	 *
	 * @var SecurityLevel
	 */
	public readonly SecurityLevel $security;

	/**
	 * Parameters for this endpoint in an array of EndpointParameters.
	 *
	 * Parameters given in this array will be proviced to run()
	 *
	 * @var EndpointParameter[]
	 */
	public readonly array $params;

	/**
	 * Load the information in
	 *
	 * @param string $apiBase Base URL (including scheme) for the REST API.
	 */
	public function __construct(
		public readonly string $apiBase
	) {
	}
}
