<?php

namespace Smolblog\Framework\Exceptions;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Throwable;

/**
 * Exception for when an error is found in the ServiceRegistrar configuration.
 */
class ServiceRegistrarConfigurationException extends Exception implements
	SmolblogException,
	ContainerExceptionInterface {
	/**
	 * Construct the exception
	 *
	 * @param string         $service  Service that was not found.
	 * @param mixed          $config   The configuration entry for $service.
	 * @param string         $message  The Exception message to throw.
	 * @param integer        $code     The Exception message to throw.
	 * @param Throwable|null $previous The previous exception used for the exception chaining.
	 */
	public function __construct(
		public readonly string $service,
		public readonly mixed $config,
		string $message = null,
		int $code = 0,
		?Throwable $previous = null
	) {
		$message ??= "Configuration error for $service in ServiceRegistrar" .
			($previous ? ': ' . $previous->getMessage() : '.');
		parent::__construct($message, $code, $previous);
	}
}
