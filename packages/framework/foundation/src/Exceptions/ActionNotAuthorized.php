<?php

namespace Smolblog\Foundation\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when an action is not authorized to run.
 *
 * A generic exception that essentially represents an HTTP 403 (Forbidden) error.
 */
class ActionNotAuthorized extends Exception {
	/**
	 * Construct the exception
	 *
	 * @param string         $message  The Exception command to throw.
	 * @param integer        $code     The Exception command to throw.
	 * @param Throwable|null $previous The previous exception used for the exception chaining.
	 */
	public function __construct(
		string $message = "",
		int $code = 0,
		?Throwable $previous = null
	) {
		parent::__construct($message, $code, $previous);
	}
}
