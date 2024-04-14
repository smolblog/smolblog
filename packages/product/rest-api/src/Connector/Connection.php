<?php

namespace Smolblog\Api\Connector;

use Smolblog\Api\ArrayType;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value;

/**
 * A connection to an external content service.
 */
readonly class Connection extends Value {
	/**
	 * Construct the Connection
	 *
	 * @param Identifier $id          ID of this connection. Created from provider and providerKey.
	 * @param Identifier $userId      ID of the user that owns this connection.
	 * @param string     $provider    Provider this connection is for.
	 * @param string     $providerKey How this account is identified by the provider.
	 * @param string     $displayName Human-readable name for the account.
	 * @param Channel[]  $channels    Channels enabled by this Connection.
	 */
	public function __construct(
		public readonly Identifier $id,
		public readonly Identifier $userId,
		public readonly string $provider,
		public readonly string $providerKey,
		public readonly string $displayName,
		#[ArrayType(Channel::class)] public readonly array $channels,
	) {
	}
}
