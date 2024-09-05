<?php

namespace Smolblog\Foundation\Exceptions;

use Exception;
use Smolblog\Foundation\Value\Messages\Command;
use Throwable;

/**
 * Exception thrown when a command is not authorized to run.
 */
class CommandNotAuthorized extends Exception {
	/**
	 * Construct the exception
	 *
	 * @param AuthorizableCommand $originalCommand Original command.
	 * @param string              $message         The Exception command to throw.
	 * @param integer             $code            The Exception command to throw.
	 * @param Throwable|null      $previous        The previous exception used for the exception chaining.
	 */
	public function __construct(
		public readonly Command $originalCommand,
		string $message = "",
		int $code = 0,
		?Throwable $previous = null
	) {
		parent::__construct($message, $code, $previous);
	}
}
