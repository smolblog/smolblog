<?php

namespace Smolblog\Foundation\v2\Value\Attributes;

use Attribute;
use Crell\Serde\Attributes\SequenceField;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ListType extends SequenceField {
	public function __construct(string $type) {
		parent::__construct(arrayType: $type);
	}
}
