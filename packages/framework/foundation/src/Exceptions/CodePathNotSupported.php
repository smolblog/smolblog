<?php

namespace Smolblog\Foundation\Exceptions;

use LogicException;
use Psr\Container\ContainerExceptionInterface;
use Throwable;

/**
 * Exception for when a code path is not supported or a configuration is incorrect.
 *
 * - This indicates an issue in code that should be fixed.
 * - This does not indicate bad input.
 *
 * For example, if a class using SerializableValueKit has a property with a union type or a type that does not
 * implement SerializableValue, the code in SerializableValueKit will not work. The class should either override the
 * necessary methods or use its own implmentation of SerializableValue.
 *
 * This should be a HTTP 500 error if it is caught by an API layer.
 */
class CodePathNotSupported extends LogicException implements ContainerExceptionInterface {
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
		parent::__construct($locationFragment . $messageFragment, $code ?? 0, $previous);
	}
}
