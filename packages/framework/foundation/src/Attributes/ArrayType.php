<?php

namespace Smolblog\Framework\Foundation\Attributes;

use Attribute;

/**
 * Attach type information to an array.
 */
#[Attribute(
	Attribute::TARGET_PROPERTY |
	Attribute::TARGET_FUNCTION |
	Attribute::TARGET_METHOD |
	Attribute::TARGET_PARAMETER
)]
class ArrayType {
	/**
	 * Construct the attribute.
	 *
	 * @param string $type Class name or primitive type constant.
	 */
	public function __construct(public readonly string $type) {
	}

	public const TYPE_ARRAY = '__builtin_array';
	public const TYPE_STRING = '__builtin_string';
	public const TYPE_INTEGER = '__builtin_integer';
	public const TYPE_BOOLEAN = '__builtin_boolean';
	public const TYPE_FLOAT = '__builtin_float';
}
