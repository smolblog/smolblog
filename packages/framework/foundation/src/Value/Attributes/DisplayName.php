<?php

namespace Smolblog\Foundation\Value\Attributes;

use Attribute;

/**
 * Attach a human-readable name to a field.
 */
#[Attribute(
	Attribute::TARGET_PROPERTY |
	Attribute::TARGET_FUNCTION |
	Attribute::TARGET_METHOD |
	Attribute::TARGET_PARAMETER
)]
class DisplayName {
	/**
	 * Construct the attribute.
	 *
	 * @param string $name Human-readable display name.
	 */
	public function __construct(public readonly string $name) {
	}
}
