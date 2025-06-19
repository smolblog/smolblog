<?php

namespace Smolblog\Foundation\Value\Attributes;

use Attribute;

/**
 * Attach type information to an Identifier or other pointer.
 */
#[Attribute(
	Attribute::TARGET_PROPERTY |
	Attribute::TARGET_FUNCTION |
	Attribute::TARGET_METHOD |
	Attribute::TARGET_PARAMETER
)]
class Target {
	/**
	 * Construct the attribute.
	 *
	 * @param class-string|string $type Class or application-specific key that denotes what this identifies.
	 */
	public function __construct(public readonly string $type) {
	}
}
