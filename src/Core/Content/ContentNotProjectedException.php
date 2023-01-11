<?php

namespace Smolblog\Core\Content;

use Exception;
use Smolblog\Core\Content\Events\ContentEvent;
use Smolblog\Framework\Exceptions\SmolblogException;
use Throwable;

/**
 * Indicates the ContentEvent has not been projected and does not have the current state defined yet.
 *
 * This happened because either the event was accessed before the execution layer (priority 0) or the projection did
 * not correctly set the current state.
 */
class ContentNotProjectedException extends Exception implements SmolblogException {
	/**
	 * Construct the Exception
	 *
	 * @param ContentEvent   $event    Event that was not defined.
	 * @param string|null    $message  The Exception message to throw.
	 * @param integer        $code     The Exception message to throw.
	 * @param Throwable|null $previous The previous exception used for the exception chaining.
	 */
	public function __construct(
		public readonly ContentEvent $event,
		string $message = null,
		int $code = 0,
		?Throwable $previous = null
	) {
		$message ??= 'Content was not defined for event ' . get_class($event) . '.';
		parent::__construct($message, $code, $previous);
	}
}
