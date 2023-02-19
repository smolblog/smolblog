<?php

namespace Smolblog\RestApiBase;

use Smolblog\Framework\Objects\ExtendableValueKit;
use Smolblog\Framework\Objects\Value;

/**
 * Value object with runtime arguments only.
 *
 * Useful for small, one-off response objects. Endpoints using this should also define the shape of the response in
 * their configuration.
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
