<?php

namespace Smolblog\Foundation\v2\Reflection;

use Crell\Serde\ValueType;

/**
 * @internal Use ListType or MapType attributes.
 */
trait ArrayTypeKit {
	private function checkPrimitive(string $type): string|ValueType {
		return match ($type) {
			'string' => ValueType::String,
			'int' => ValueType::Int,
			'float' => ValueType::Float,
			'array' => ValueType::Array,
			default => $type,
		};
	}
}
