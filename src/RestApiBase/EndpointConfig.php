<?php

namespace Smolblog\RestApiBase;

/**
 * Configuration data for an Endpoint used to register it with the outside router.
 */
class EndpointConfig {
	/**
	 * The given route for this endpoint.
	 *
	 * If the endpoint is `smolblog.com/api/blog/info`, then this value should be `/blog/info` or `blog/info` (the
	 * opening slash will be inferred).
	 *
	 * To declare URL parameters, add the parameter's slug within curly braces (e.g. `post/{id}/comments`) then declare
	 * them in the $pathVariables property.
	 *
	 * @var string
	 */
	public readonly string $route;

	/**
	 * HTTP verbs this endpoint will respond to.
	 *
	 * Given as an array of Verb enum instances.
	 *
	 * @var Verb[]
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
	 */
	public readonly array $verbs;

	/**
	 * Parameters for this endpoint's path in an array of strings.
	 *
	 * The array should be keyed to the same words used in $route; the value is a ParameterType to validate it.
	 * parameter.
	 *
	 * @var ParameterType[]
	 */
	public readonly array $pathVariables;

	/**
	 * Parameters this endpoint accepts in the query string.
	 *
	 * The array should be keyed to any valid parameters with the value being the ParameterType to validate it.
	 *
	 * @var string[]
	 */
	public readonly array $queryVariables;

	/**
	 * Fully qualified class name that the request body should serialize to/from.
	 *
	 * @var string|null
	 */
	public readonly ?string $bodyClass;

	/**
	 * Indicate if this is a public endpoint.
	 *
	 * A public endpoint does not require authentication. It could behave differently if a user is authenticated, but it
	 * is not *required*. Setting this value to `false` will cause any unauthenticated requests to return with a 401
	 * status code.
	 *
	 * @var boolean
	 */
	public readonly bool $public;

	/**
	 * Create the configuration object.
	 *
	 * @param string      $route          The given route for this endpoint.
	 * @param array       $verbs          HTTP verbs this endpoint will respond to.
	 * @param array       $pathVariables  Parameters for this endpoint's path in an array of strings.
	 * @param array       $queryVariables Parameters this endpoint accepts in the query string.
	 * @param string|null $bodyClass      Fully qualified class name that the request body should serialize to/from.
	 * @param boolean     $public         Indicate if this is a public endpoint.
	 */
	public function __construct(
		string $route,
		array $verbs = [Verb::GET],
		array $pathVariables = [],
		array $queryVariables = [],
		?string $bodyClass = null,
		bool $public = true,
	) {
		$this->route = $route;
		$this->verbs = $verbs;
		$this->$pathVariables = $pathVariables;
		$this->queryVariables = $queryVariables;
		$this->bodyClass = $bodyClass;
		$this->public = $public;
	}
}
