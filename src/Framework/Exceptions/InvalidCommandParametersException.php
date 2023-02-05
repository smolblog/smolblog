<?php

namespace Smolblog\Framework\Exceptions;

use Exception;
use Smolblog\Framework\Messages\Command;
use Throwable;

/**
 * Exception for when an error is found in the ServiceRegistrar configuration.
 */
class InvalidCommandParametersException extends Exception implements SmolblogException {
	/**
	 * Construct the exception
	 *
	 * @param Command        $command  Command with invalid parameters.
	 * @param string         $message  The Exception message to throw.
	 * @param integer        $code     The Exception message to throw.
	 * @param Throwable|null $previous The previous exception used for the exception chaining.
	 */
	public function __construct(
		public readonly Command $command,
		string $message = null,
		int $code = 0,
		?Throwable $previous = null
	) {
		$message ??= 'Invalid parameters given to command ' . get_class($command);
		parent::__construct($message, $code, $previous);
	}
}
