<?php

namespace Smolblog\RestApiBase;

use JsonSerializable;
use Smolblog\Framework\Objects\Value;

/**
 * Represents a response to a REST API request.
 */
class Response {
	/**
	 * Construct the response.
	 *
	 * @param JsonSerializable|array $body   Data of the response.
	 * @param integer                $status HTTP status to return.
	 */
	public function __construct(
		public readonly JsonSerializable|array $body,
		public readonly int $status,
	) {
	}
}
