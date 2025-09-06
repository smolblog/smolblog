<?php

namespace Smolblog\Foundation\v2\Reflection;

use Attribute;
use Crell\Serde\Attributes\DictionaryField;

/**
 * Indicates the property is an associative array (a.k.a Hash, Dictionary, Map).
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class MapType extends DictionaryField implements ArrayType {
	use ArrayTypeKit;

	/**
	 * @param class-string $type Class or type for this array's values.
	 */
	public function __construct(string $type) {
		parent::__construct(arrayType: $this->checkPrimitive($type));
	}
}
