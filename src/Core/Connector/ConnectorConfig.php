<?php

namespace Smolblog\Core\Connector;

/**
 * Configuration information about a Connector
 */
class ConnectorConfig {
	/**
	 * Create the configuration object.
	 *
	 * @param string $slug URL-friendly identifier for the Connector.
	 */
	public function __construct(
		public readonly string $slug
	) {
	}
}
