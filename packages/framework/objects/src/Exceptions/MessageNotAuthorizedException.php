<?php

namespace Smolblog\Framework\Exceptions;

use Exception;
use Smolblog\Foundation\Value\Messages\Query;
use Throwable;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Framework\Messages\Query as DeprecatedQuery;

/**
 * Exception thrown when a message is not authorized to run.
 *
 * @deprecated Migrate to Smolblog\Foundation classes
 */
class MessageNotAuthorizedException extends Exception implements SmolblogException {
	/**
	 * Construct the exception
	 *
	 * @param AuthorizableMessage   $originalMessage    Original message.
	 * @param DeprecatedQuery|Query $authorizationQuery Query that returned falsey value.
	 * @param string                $message            The Exception message to throw.
	 * @param integer               $code               The Exception message to throw.
	 * @param Throwable|null        $previous           The previous exception used for the exception chaining.
	 */
	public function __construct(
		public readonly AuthorizableMessage $originalMessage,
		public readonly DeprecatedQuery|Query $authorizationQuery,
		string $message = "",
		int $code = 0,
		?Throwable $previous = null
	) {
		parent::__construct($message, $code, $previous);
	}
}
