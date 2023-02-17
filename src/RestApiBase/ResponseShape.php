<?php

namespace Smolblog\RestApiBase;

use Attribute;

/**
 * Manually declare the schema of an endpoint response.
 *
 * Useful if the endpoint returns a unique or nonstandard response that doesn't merit creating its own class. Follows
 * the same format as EndpointConfig::bodyShape.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class ResponseShape {
	/**
	 * Construct the attribute
	 *
	 * @param array $shape Schema for the API response.
	 */
	public function __construct(public readonly array $shape) {
	}
}
