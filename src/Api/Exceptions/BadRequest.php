<?php

namespace Smolblog\Api\Exceptions;

use Exception;

/**
 * Return a 404 not found error.
 */
class BadRequest extends ErrorResponse {
	/**
	 * Get the HTTP error code.
	 *
	 * @return integer
	 */
	public function getHttpCode(): int {
		return 400;
	}
}
