<?php

namespace Smolblog\Api;

use Attribute;

/**
 * Attach type information to an array.
 */
#[Attribute]
class ArrayType {
	/**
	 * Construct the attribute.
	 *
	 * @param string|array $type Class name, primitive type, or OpenAPI schema.
	 */
	public function __construct(public readonly string|array $type) {
	}
}
