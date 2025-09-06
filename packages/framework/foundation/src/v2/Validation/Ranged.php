<?php

namespace Smolblog\Foundation\v2\Validation;

use Attribute;

/**
 * Indicate that the property should be limited to a range of values.
 *
 * For integer and float numbers, this is a range of values. For strings, it is a range of characters. For arrays, it
 * is a range of number of entries.
 *
 * Ranges are inclusive by default; set $exclusive to change.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Ranged {
	/**
	 * @param integer|float|null|null $min       Minimum value.
	 * @param integer|float|null|null $max       Maximum value.
	 * @param boolean                 $exclusive True if the exact values for $min and $max are not valid.
	 */
	public function __construct(
		public int|float|null $min = null,
		public int|float|null $max = null,
		public bool $exclusive = false,
	) {
	}
}
