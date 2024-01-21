<?php

namespace Smolblog\Framework\Exceptions;

use Exception;
use Throwable;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Query;

/**
 * Exception thrown when a message is not authorized to run.
 */
class MessageNotAuthorizedException extends Exception implements SmolblogException {
	/**
	 * Construct the exception
	 *
	 * @param AuthorizableMessage $originalMessage    Original message.
	 * @param Query               $authorizationQuery Query that returned falsey value.
	 * @param string              $message            The Exception message to throw.
	 * @param integer             $code               The Exception message to throw.
	 * @param Throwable|null      $previous           The previous exception used for the exception chaining.
	 */
	public function __construct(
		public readonly AuthorizableMessage $originalMessage,
		public readonly Query $authorizationQuery,
		string $message = "",
		int $code = 0,
		?Throwable $previous = null
	) {
		parent::__construct($message, $code, $previous);
	}
}
