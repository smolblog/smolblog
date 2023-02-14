<?php

namespace Smolblog\RestApiBase\Responses;

use Smolblog\Framework\Objects\ArraySerializable;
use Smolblog\RestApiBase\Response;

/**
 * A successful request deserves a successful response!
 */
class SuccessResponse extends Response {
	/**
	 * Construct the response
	 *
	 * @param ArraySerializable|array $results Results to return.
	 */
	public function __construct(ArraySerializable|array $results) {
		$resultsArray = is_array($results) ? $results : $results->toArray();

		parent::__construct(
			body: $resultsArray,
			status: 200
		);
	}
}
