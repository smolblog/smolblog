<?php

namespace Smolblog\Framework\Exceptions;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Throwable;

/**
 * Exception for when a service found in the ServiceRegistrar but no implementation can be found.
 */
class ServiceNotImplementedException extends Exception implements SmolblogException, ContainerExceptionInterface {
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
		$message ??= "Class $service not found.";
		parent::__construct($message, $code, $previous);
	}
}
