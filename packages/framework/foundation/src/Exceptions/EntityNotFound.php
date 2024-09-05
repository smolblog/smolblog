<?php

namespace Smolblog\Foundation\Exceptions;

use Exception;
use Smolblog\Foundation\Value\Fields\Identifier;
use Throwable;

/**
 * Exception thrown when an ID is given that does not correspond to an Entity.
 */
class EntityNotFound extends Exception {
	/**
	 * Construct the exception
	 *
	 * @param string|Identifier $entityId   ID that does not correspond to an Entity.
	 * @param string            $entityName Name of the Entity.
	 * @param string            $message    The Exception command to throw.
	 * @param integer           $code       The Exception command to throw.
	 * @param Throwable|null    $previous   The previous exception used for the exception chaining.
	 */
	public function __construct(
		public readonly string|Identifier $entityId,
		public readonly string $entityName,
		?string $message = null,
		int $code = 0,
		?Throwable $previous = null
	) {
		$message ??= "No $entityName found with ID $entityId";
		parent::__construct($message, $code, $previous);
	}
}
