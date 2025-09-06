<?php

namespace Smolblog\Foundation\v2\Reflection;

use Attribute;
use Crell\Serde\Attributes\SequenceField;

/**
 * Indicates that this array is a list.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ListType extends SequenceField implements ArrayType {
	use ArrayTypeKit;

	/**
	 * @param class-string $type Class or type for this array's values.
	 */
	public function __construct(string $type) {
		parent::__construct(arrayType: $this->checkPrimitive($type));
	}
}
