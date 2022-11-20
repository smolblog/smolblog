<?php

namespace Smolblog\App\Hooks;

/**
 * Fired when the Smolblog core system is registering endpoints. Event consumers can add
 * their own endpoints to the array and/or remove existing endpoints. The array is fully-qualified
 * class names that have already been added to the App's container.
 */
class CollectingEndpoints {
	/**
	 * Construct the event
	 *
	 * @param array $endpoints Array of fully-qualified class names of Endpoints.
	 */
	public function __construct(public array $endpoints) {
	}
}
