<?php

namespace Smolblog\Foundation\Value\Traits;

use Attribute;
use Smolblog\Foundation\Value\Attributes\ArrayType as AttributesArrayType;

/**
 * Attach type information to an array.
 *
 * @deprecated use Smolblog\Foundation\Value\Attributes\ArrayType
 */
#[Attribute(
	Attribute::TARGET_PROPERTY |
	Attribute::TARGET_FUNCTION |
	Attribute::TARGET_METHOD |
	Attribute::TARGET_PARAMETER
)]
class ArrayType extends AttributesArrayType {
}
