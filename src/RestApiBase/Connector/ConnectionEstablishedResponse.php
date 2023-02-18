<?php

namespace Smolblog\RestApiBase\Connector;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;
use Smolblog\RestApiBase\ParameterType;

/**
 * Response when a Connection is established.
 */
class ConnectionEstablishedResponse extends Value {
	/**
	 * Construct the response
	 *
	 * @param Identifier $id          ID for the new Connection.
	 * @param string     $provider    Provider for the Connection.
	 * @param string     $displayName Visible name for the Connection.
	 * @param string[]   $channels    List of Channels this Connection provides.
	 */
	public function __construct(
		public readonly Identifier $id,
		public readonly string $provider,
		public readonly string $displayName,
		#[ParameterType(type: 'array', item: 'string')] public readonly array $channels,
	) {
	}
}
