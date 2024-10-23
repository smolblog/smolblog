<?php

namespace Smolblog\Foundation\Value\Jobs;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\SerializableSupertypeKit;
use Smolblog\Foundation\Value\Traits\SerializableValue;

/**
 * A Job represents a task that should be performed asynchronously.
 *
 * A job can be routed to any method on any service. Realistically, it should be a service in a dependency injection
 * container. Additional properties will be passed as parameters; override `getParameters` to change this.
 *
 * Extend this class and add any needed information. Jobs will likely be serialized to facilitate cross-thread or
 * cross-server communication.
 */
readonly abstract class Job extends Value implements SerializableValue {
	use SerializableSupertypeKit;

	/**
	 * Construct the job.
	 *
	 * @param class-string $service Service to instantiate.
	 * @param string       $method  Method on $service to call.
	 */
	public function __construct(
		public string $service,
		public string $method,
	) {
	}

	/**
	 * Get the parameters to be passed to $service->$method.
	 *
	 * Default implementation is any properties on the object excluding $service and $method.
	 *
	 * @return array
	 */
	public function getParameters(): array {
		$base = \get_object_vars($this);
		unset($base['service'], $base['method']);
		return $base;
	}
}
