<?php

namespace Smolblog\Foundation\Value\Attributes;

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
	 * @param string  $type  Class name or primitive type constant.
	 * @param boolean $isMap True if this array is a map (has string keys).
	 */
	public function __construct(public readonly string $type, public readonly bool $isMap = false) {
	}

	/**
	 * True if this ArrayType is one of the built-in types.
	 *
	 * @return boolean
	 */
	public function isBuiltIn(): bool {
		return \in_array($this->type, [
			self::TYPE_STRING,
			self::TYPE_INTEGER,
			self::TYPE_BOOLEAN,
			self::TYPE_FLOAT,
		]);
	}

	public const TYPE_STRING = 'string';
	public const TYPE_INTEGER = 'int';
	public const TYPE_BOOLEAN = 'bool';
	public const TYPE_FLOAT = 'float';
	public const NO_TYPE = '__no_type';
}
