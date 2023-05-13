<?php

namespace Smolblog\Framework\Exceptions;

use Exception;
use Throwable;

/**
 * Indicates an invalid class was attempted to be registered.
 *
 * This is thrown by RegistrarKit when register is called on a class that does not implement the required interface.
 */
class RegistrationException extends Exception implements SmolblogException {
	/**
	 * Construct the exception
	 *
	 * @param string         $message  The Exception message to throw.
	 * @param integer        $code     The Exception message to throw.
	 * @param Throwable|null $previous The previous exception used for the exception chaining.
	 */
	public function __construct(
		string $message = null,
		int $code = 0,
		?Throwable $previous = null
	) {
		parent::__construct($message, $code, $previous);
	}
}
