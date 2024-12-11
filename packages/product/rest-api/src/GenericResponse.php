<?php

namespace Smolblog\Api;

use Smolblog\Framework\Objects\ExtendableValueKit;
use Smolblog\Framework\Objects\Value;

/**
 * Value object with runtime arguments only.
 *
 * Useful for small, one-off response objects. Endpoints using this should also define the shape of the response in
 * their configuration.
 *
 * @deprecated Just use an array.
 */
class GenericResponse extends Value {
	use ExtendableValueKit;

	/**
	 * Construct the response.
	 *
	 * @param mixed ...$props Properties of the response.
	 */
	public function __construct(mixed ...$props) {
		$this->extendedFields = $props;
	}
}
