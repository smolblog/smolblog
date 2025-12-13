<?php

namespace Smolblog\Foundation\v2\DomainModel;

use Attribute;
use Smolblog\Foundation\v2\Reflection\ListType;
use Smolblog\Foundation\v2\Reflection\MapType;

/**
 * Document the expected response from handling a Command.
 *
 * Sometimes the result of a Command is used by the calling code. This documents what the expected type is (either a
 * builtin type, fully-qualified class name, or a MapType/ListType) along with an optional name and description (used in
 * generating a REST endpoint).
 */
#[Attribute(Attribute::TARGET_CLASS)]
class ExpectedResponse {
	/**
	 * Construct the attribute.
	 *
	 * @param class-string|ListType|MapType $type        Type of the response.
	 * @param boolean                       $optional    True if a valid (non-Exceptional)
	 *                                                   response can also be null.
	 * @param string|null                   $name        Optional name for the response.
	 * @param string|null                   $description Optional description for the response.
	 */
	public function __construct(
		public readonly string|ListType|MapType $type,
		public readonly bool $optional = false,
		public readonly ?string $name = null,
		public readonly ?string $description = null,
	) {
	}
}
