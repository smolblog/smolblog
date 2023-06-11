<?php

namespace Smolblog\Api\Connector;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * An external content source or destination.
 */
class Channel extends Value {
	/**
	 * Construct the Channel
	 *
	 * @param Identifier  $id                 ID for this channel; created from the Connection ID and channelKey.
	 * @param string      $channelKey         Unique identifier for this channel within the Connection.
	 * @param string      $displayName        Human-readable name for the Channel.
	 * @param string|null $connectionProvider Slug for the provider this channel connects to.
	 * @param string|null $connectionName     Human-readable name for this channel's Connection.
	 */
	public function __construct(
		public readonly Identifier $id,
		public readonly string $channelKey,
		public readonly string $displayName,
		public readonly ?string $connectionProvider = null,
		public readonly ?string $connectionName = null,
	) {
	}

	/**
	 * Serialize this object.
	 *
	 * @return array
	 */
	public function toArray(): array {
		return [
			...parent::toArray(),
			'id' => $this->id->toString(),
		];
	}
}
