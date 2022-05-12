<?php

namespace Smolblog\Core\Exceptions;

use Smolblog\Core\Environment;

/**
 * Exception for errors in the current Environment.
 */
class EnvironmentException extends SmolblogException {
	/**
	 * Store the Environment object that threw the exception.
	 *
	 * @var Environment
	 */
	private Environment $currentEnv;

	/**
	 * Construct the object. Adds $environment as a required parameter
	 *
	 * @param Environment    $environment The Environment throwing the Exception.
	 * @param string         $message     Message for this particular Exception.
	 * @param integer        $code        Code for this Exception.
	 * @param Throwable|null $previous    The previous Exception.
	 */
	public function __construct(
		?Environment $environment,
		string $message = '',
		int $code = 0,
		Throwable $previous = null
	) {
		$currentEnv = $environment;
		parent::__construct($message, $code, $previous);
	}

	/**
	 * Get the Environment object that threw the Exception.
	 *
	 * @return Environment
	 */
	final public function getEnvironment(): Environment {
		return $currentEnv;
	}
}
