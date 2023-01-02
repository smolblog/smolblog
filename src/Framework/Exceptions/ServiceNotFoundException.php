<?php

namespace Smolblog\Framework\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * Exception for when a service is asked for but not found in the ServiceRegistrar.
 */
class ServiceNotFoundException extends Exception implements SmolblogException, NotFoundExceptionInterface {
	/**
	 * Construct the exception
	 *
	 * @param string         $service  Service that was not found.
	 * @param string         $message  The Exception message to throw.
	 * @param integer        $code     The Exception message to throw.
	 * @param Throwable|null $previous The previous exception used for the exception chaining.
	 */
	public function __construct(
		public readonly string $service,
		string $message = null,
		int $code = 0,
		?Throwable $previous = null
	) {
		$message ??= "Service $service not found in ClassRegistrar.";
		parent::__construct($message, $code, $previous);
	}
}
