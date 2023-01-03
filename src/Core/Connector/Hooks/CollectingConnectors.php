<?php

namespace Smolblog\Core\Connector\Hooks;

use Smolblog\Framework\Messages\Hook;

/**
 * Hook to allow other models to add Connectors to the ConnectorRegistrar.
 */
class CollectingConnectors extends Hook {
	/**
	 * Connectors to be registered. Should be in the format provider => class.
	 *
	 * @var array
	 */
	public array $connectors = [];
}
