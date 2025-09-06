<?php

namespace Smolblog\Foundation\v2\Validation;

use Attribute;

/**
 * For classes that require at least one of a set of properties.
 *
 * Will check that at least one of the listed properties is not null.
 */
#[Attribute(Attribute::TARGET_CLASS)]
readonly class AtLeastOneOf {
	/**
	 * @var string[]
	 */
	public array $properties;

	/**
	 * @param string ...$properties Class properties to check.
	 */
	public function __construct(string ...$properties) {
		$this->properties = $properties;
	}
}
