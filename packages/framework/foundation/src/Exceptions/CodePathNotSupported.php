<?php

namespace Smolblog\Foundation\Exceptions;

use LogicException;
use Throwable;

/**
 * Exception for when a code path is not supported. This could be a method that is not implemented or a property that
 * is not supported.
 */
class CodePathNotSupported extends LogicException {
	/**
	 * Construct the exception
	 *
	 * @param string|null $message  Optional message.
	 * @param integer     $code     Optional code.
	 * @param Throwable   $previous Optional previous exception.
	 * @param string|null $location Optional descriptive location of the code path.
	 */
	public function __construct(
		?string $message = null,
		?int $code = 0,
		?Throwable $previous = null,
		?string $location = null,
	) {
		$locationFragment = isset($location) ? "In $location: " : '';
		$messageFragment = isset($message) ? $message : 'The code path is not supported.';
		parent::__construct($locationFragment . $messageFragment, $code, $previous);
	}
}
