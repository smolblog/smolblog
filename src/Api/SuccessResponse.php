<?php

namespace Smolblog\Api;

use Smolblog\Framework\Objects\Value;

/**
 * Simple response for successful operations.
 */
class SuccessResponse extends Value {
	/**
	 * Construct the response
	 *
	 * @param boolean $success Default true.
	 */
	public function __construct(public readonly bool $success = true) {
	}
}
