<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
	/**
	 * Store (mock) dependencies for the service.
	 *
	 * @var array
	 */
	protected array $deps = [];

	/**
	 * Store a
	 *
	 * @var mixed
	 */
	protected mixed $service;

	/**
	 * Build the given service using $this->deps.
	 *
	 * @param string $class Fully-qualified class name of service to instantiate.
	 * @return mixed
	 */
	protected function buildService(string $class): mixed {
		return new $class(...$this->deps);
	}
}
