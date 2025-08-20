<?php

namespace Smolblog\Foundation\v2\Value\Attributes;

use Attribute;
use Crell\Serde\Attributes\DictionaryField;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MapType extends DictionaryField {
	public function __construct(string $type) {
		parent::__construct(arrayType: $type);
	}
}
