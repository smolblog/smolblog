<?php

namespace Smolblog\Core\Channel\Services;

use Exception;
use Throwable;

/**
 * Exception class to denote a failure in the content push process.
 */
class ContentPushException extends Exception {
	/**
	 * Create the exception.
	 *
	 * @param string         $message  User-friendly message describing the failure.
	 * @param array          $details  Applicable details that should be logged.
	 * @param integer        $code     The exception code.
	 * @param Throwable|null $previous The previously thrown error.
	 */
	public function __construct(
		string $message,
		public readonly array $details,
		int $code = 0,
		?Throwable $previous = null,
	) {
		parent::__construct($message, $code, $previous);
	}
}
