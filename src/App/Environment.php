<?php

namespace Smolblog\App;

use JsonSerializable;
use Smolblog\Framework\ExtendableValue;

/**
 * Environment information for the App
 */
readonly class Environment extends ExtendableValue {
	/**
	 * Base URL (including scheme and trailing slash) for the REST API.
	 *
	 * @var string
	 */
	public readonly string $apiBase;

	/**
	 * Load the information in
	 *
	 * @param string $apiBase    Base URL (including scheme and trailing slash) for the REST API.
	 * @param mixed  ...$envVars Arbitrary environment variables.
	 */
	public function __construct(string $apiBase, mixed ...$envVars) {
		$this->apiBase = rtrim($apiBase, '/') . '/';
		parent::__construct(...$envVars);
	}
}
