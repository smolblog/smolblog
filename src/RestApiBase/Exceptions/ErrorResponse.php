<?php

namespace Smolblog\RestApiBase\Exceptions;

use Exception;
use JsonSerializable;
use Smolblog\Framework\Exceptions\SmolblogException;
use Smolblog\RestApiBase\DataType;

/**
 * Generic exception to create an error response for the API.
 */
abstract class ErrorResponse extends Exception implements SmolblogException, JsonSerializable {
	/**
	 * Create a standard JSON representation.
	 *
	 * @return array
	 */
	public function jsonSerialize(): array {
		return ['code' => $this->getHttpCode(), 'error' => $this->getMessage()];
	}

	/**
	 * Get the HTTP code this response should use.
	 *
	 * @return integer
	 */
	public function getHttpCode(): int {
		return 500;
	}

	public const SCHEMA = [
		'application/json' => [
			'schema' => [
				'type' => 'object',
				'properties' => [
					'code' => ['type' => 'integer'],
					'error' => ['type' => 'string'],
				],
				'required' => ['code', 'error'],
			]
		]
	];
}
