<?php

namespace Smolblog\App\Endpoint;

use Smolblog\Framework\Objects\Value;

/**
 * Configuration data for an Endpoint used to register it with the outside router.
 */
class EndpointConfig extends Value {
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
	 * Parameters for this endpoint's URL in an array of strings:
	 *   $parameterSlug => $regularExpressionToValidate
	 *
	 * @var string[]
	 */
	public readonly array $params;

	/**
	 * Load the data
	 *
	 * @param string        $route    Route for the endpoint. Required.
	 * @param HttpVerb[]    $verbs    HTTP verbs the endpoint responds to. Defaults to GET.
	 * @param SecurityLevel $security Security level for the endpoint. Defaults to Anonymous.
	 * @param string[]      $params   Array of parameters used in the route. Key is the slug, value is the RegEx.
	 */
	public function __construct(
		string $route,
		array $verbs = [HttpVerb::GET],
		SecurityLevel $security = SecurityLevel::Anonymous,
		array $params = []
	) {
		$this->route = $route;
		$this->verbs = $verbs;
		$this->security = $security;
		$this->params = $params;
	}
}
