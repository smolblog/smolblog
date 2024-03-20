<?php

namespace Smolblog\Framework\Foundation\Exceptions;

use InvalidArgumentException;
use Throwable;

/**
 * Exception for when a value has invalid properties.
 */
class InvalidValueProperties extends InvalidArgumentException {
	/**
	 * Construct the exception
	 *
	 * @param string|null $message  Optional message.
	 * @param integer     $code     Optional code.
	 * @param Throwable   $previous Optional previous exception.
	 */
	public function __construct(
		?string $message = null,
		?int $code = 0,
		?Throwable $previous = null,
	) {
		parent::__construct($message, $code, $previous);
	}
}
