<?php

namespace Smolblog\Test\Kits;
use stdClass;

/**
 * Quick setup for testing Service classes.
 */
trait ServiceTestKit {
	/**
	 * Store (mock) dependencies for the service.
	 *
	 * @var stdClass
	 */
	private stdClass $deps;

	/**
	 * Build the given service with mocks for each dependency.
	 *
	 * Dependencies will be added to $this->deps according to the parameter names on the constructor. If you want to
	 * override with your own mocks, pass them as additional named parameters to this method.
	 *
	 * @param string $class Fully-qualified class name of service to instantiate.
	 * @param mixed ...$overrides Any constructor parameters to override.
	 * @return mixed
	 */
	private function setUpService(string $class, mixed ...$overrides): mixed {
		$params = (new \ReflectionClass($class))->getConstructor()->getParameters();
		$this->deps = new stdClass();
		foreach($params as $param) {
			$name = $param->getName();
			if (isset($overrides[$name])) {
				$this->deps->$name = $overrides[$name];
				continue;
			}

			$this->deps->$name = $this->createMock($param->getType()->__toString());
		}

		return new $class(...(array)$this->deps);
	}
}
