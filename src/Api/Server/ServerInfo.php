<?php

namespace Smolblog\Api\Server;

use Smolblog\Framework\Objects\Value;

/**
 * Describe standard information about the server.
 */
class ServerInfo extends Value {
	/**
	 * Construct the object.
	 *
	 * @param string      $serverVersion Version of Smolblog this server is running.
	 * @param string      $specHref      Link to the OpenAPI spec for this server.
	 * @param string|null $license       Applicable license information.
	 */
	public function __construct(
		public readonly string $serverVersion,
		public readonly string $specHref,
		public readonly ?string $license = null,
	) {
	}
}
