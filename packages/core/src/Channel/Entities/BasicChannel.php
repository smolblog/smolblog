<?php

namespace Smolblog\Core\Channel\Entities;

use Cavatappi\Foundation\Reflection\MapType;
use Ramsey\Uuid\UuidInterface;

/**
 * A basic Channel type that doesn't need anything special; also used as a backup for deserialization.
 *
 * This is most useful for Connection-backed Channels since those are typically created and consumed in the same
 * package and thus don't need special data structures or type checking.
 */
class BasicChannel extends Channel {
	/**
	 * Create the Channel.
	 *
	 * @param string             $handler      Key for the handler this is tied to (usually provider name).
	 * @param string             $handlerKey   Unique identifier for this account at this provider.
	 * @param string             $displayName  Recognizable name for the channel (URL or handle?).
	 * @param array              $details      Extra properties used by the handler.
	 * @param UuidInterface|null $userId       User responsible for this Channel (if applicable).
	 * @param UuidInterface|null $connectionId Connection needed to authenticate for this channel (if necessary).
	 */
	public function __construct(
		string $handler,
		string $handlerKey,
		string $displayName,
		#[MapType('mixed')] public readonly array $details,
		?UuidInterface $userId = null,
		?UuidInterface $connectionId = null,
	) {
		parent::__construct(
			handler: $handler,
			handlerKey: $handlerKey,
			displayName: $displayName,
			userId: $userId,
			connectionId: $connectionId,
		);
	}
}
