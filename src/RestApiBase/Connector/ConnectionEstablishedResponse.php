<?php

namespace Smolblog\RestApiBase\Connector;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Response when a Connection is established.
 */
class ConnectionEstablishedResponse extends Value {
	/**
	 * List of Channels this Connection provides.
	 *
	 * @var string[]
	 */
	public readonly array $channels;

	/**
	 * Construct the response
	 *
	 * @param Identifier $id ID for the new Connection.
	 * @param string $provider Provider for the Connection.
	 * @param string $displayName Visible name for the Connection.
	 * @param string[] $channels List of Channels this Connection provides.
	 */
	public function __construct(
		public readonly Identifier $id,
		public readonly string $provider,
		public readonly string $displayName,
		array $channels,
	) {
		$this->channels = $channels;
	}
}
