<?php

namespace Smolblog\Foundation\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * Exception thrown when a registry is told to retrieve a service that is not registered with it.
 *
 * This applies to dependency injection containers as well as other registries, so it satisfies
 * the PSR-11 NotFoundExceptionInterface.
 */
class ServiceNotRegistered extends Exception implements NotFoundExceptionInterface {
	/**
	 * Construct the exception
	 *
	 * @param string         $service  Name or key of the service.
	 * @param string         $registry Registry the service is not registered with.
	 * @param string         $message  The Exception command to throw.
	 * @param integer        $code     The Exception command to throw.
	 * @param Throwable|null $previous The previous exception used for the exception chaining.
	 */
	public function __construct(
		public readonly string $service,
		public readonly string $registry,
		?string $message = null,
		int $code = 0,
		?Throwable $previous = null
	) {
		$message ??= "$this->service is not registered with $this->registry";
		parent::__construct($message, $code, $previous);
	}
}
