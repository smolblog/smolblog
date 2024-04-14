<?php

namespace Smolblog\Api;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * Simple response for successful operations.
 */
readonly class SuccessResponse extends Value implements SerializableValue {
	use SerializableValueKit;

	/**
	 * Construct the response
	 *
	 * @param boolean $success Default true.
	 */
	public function __construct(public readonly bool $success = true) {
	}
}
