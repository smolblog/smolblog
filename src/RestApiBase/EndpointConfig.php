<?php

namespace Smolblog\RestApiBase;

use InvalidArgumentException;
use Smolblog\Framework\Objects\Value;

/**
 * Configuration data for an Endpoint used to register it with the outside router.
 */
class EndpointConfig extends Value {
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
	 * HTTP verb this endpoint will respond to.
	 *
	 * @var Verb
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
	 */
	public readonly Verb $verb;

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
	 * @var ParameterType[]
	 */
	public readonly array $queryVariables;

	/**
	 * Fully qualified class name that the request body should serialize to/from.
	 *
	 * @var string|null
	 */
	public readonly ?string $bodyClass;

	/**
	 * Describes the shape of the response body if it is not a defined class.
	 *
	 * @var ParameterType|null
	 */
	public readonly ?ParameterType $responseShape;

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
	 * @throws InvalidArgumentException If both or neither $bodyClass and $bodyShape are given.
	 *
	 * @param string             $route          The given route for this endpoint.
	 * @param Verb               $verb           HTTP verb this endpoint will respond to.
	 * @param array              $pathVariables  Parameters for this endpoint's path in an array of strings.
	 * @param array              $queryVariables Parameters this endpoint accepts in the query string.
	 * @param string|null        $bodyClass      Fully qualified class name that the request body should serialize from.
	 * @param ParameterType|null $responseShape  Describes the shape of the response body if it is not a defined class.
	 * @param boolean            $public         Indicate if this is a public endpoint.
	 */
	public function __construct(
		string $route,
		Verb $verb = Verb::GET,
		array $pathVariables = [],
		array $queryVariables = [],
		?string $bodyClass = null,
		?ParameterType $responseShape = null,
		bool $public = false,
	) {
		$this->route = '/' . ltrim(rtrim($route, '/'), '/');
		$this->verb = $verb;
		$this->pathVariables = $pathVariables;
		$this->queryVariables = $queryVariables;
		$this->bodyClass = $bodyClass;
		$this->responseShape = $responseShape;
		$this->public = $public;
	}
}
