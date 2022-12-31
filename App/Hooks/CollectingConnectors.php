<?php

namespace Smolblog\App\Hooks;

/**
 * Fired when the Smolblog core system is registering Connectors. Event consumers can add
 * their own connectors to the array and/or remove existing connectors. The array is fully-qualified
 * class names that have already been added to the App's container.
 */
class CollectingConnectors {
	/**
	 * Construct the event
	 *
	 * @param array $connectors Array of fully-qualified class names of Connectors.
	 */
	public function __construct(public array $connectors) {
	}
}
