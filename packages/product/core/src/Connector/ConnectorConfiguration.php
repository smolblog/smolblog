<?php

namespace Smolblog\Core\Connector;

use Smolblog\Foundation\Value;

/**
 * Configuration information for a Connection.
 */
readonly class ConnectorConfiguration extends Value {
	/**
	 * Undocumented function
	 *
	 * @param string  $key         Unique text key for the connection. Usually the name of the external service.
	 * @param boolean $pushEnabled True if this connector supports pushing content to the service.
	 * @param boolean $pullEnabled True if this connector supports pulling content from the service.
	 */
	public function __construct(
		public readonly string $key,
		public readonly bool $pushEnabled = false,
		public readonly bool $pullEnabled = false,
	) {
	}
}
