<?php

namespace Smolblog\Foundation\Value\Attributes;

use Attribute;

/**
 * Denote that property is derived and should not be included in reflection.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Derived {
	/**
	 * Construct the attribute.
	 */
	public function __construct() {
	}
}
