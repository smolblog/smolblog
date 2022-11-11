<?php

namespace Smolblog\Core\Events;

/**
 * Fired when the Smolblog core system is registering Importers. Event consumers can add
 * their own importers to the array and/or remove existing importers. The array is fully-qualified
 * class names that have already been added to the App's container.
 */
class CollectingImporters {
	/**
	 * Construct the event
	 *
	 * @param array $importers Array of fully-qualified class names of Importers.
	 */
	public function __construct(public array $importers) {
	}
}
