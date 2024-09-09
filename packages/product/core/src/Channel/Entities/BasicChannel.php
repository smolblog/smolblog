<?php

namespace Smolblog\Core\Channel\Entities;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Traits\SerializableValue;

/**
 * A basic Channel type that doesn't need anything special; also used as a backup for deserialization.
 *
 * This is most useful for Connection-backed Channels since those are typically created and consumed in the same
 * package and thus don't need special data structures or type checking.
 */
readonly class BasicChannel extends Channel {
	/**
	 * Extra properties used by the handler.
	 *
	 * @var array<array|string|float|integer|boolean>
	 */
	public array $details;

	/**
	 * Create the Channel.
	 *
	 * @param string                             $handler      Key for the handler this is tied to (usually provider
	 *                                                                                                                name).
	 * @param string                             $handlerKey   Unique identifier for this account at this provider.
	 * @param string                             $displayName  Recognizable name for the channel (URL or handle?).
	 * @param Identifier|null                    $userId       User responsible for this Channel (if applicable).
	 * @param Identifier|null                    $connectionId Connection needed to authenticate for this channel (if
	 *                    																		 necessary).
	 * @param array|string|float|integer|boolean ...$details   Extra properties used by the handler.
	 */
	public function __construct(
		string $handler,
		string $handlerKey,
		string $displayName,
		?Identifier $userId = null,
		?Identifier $connectionId = null,
		mixed ...$details,
	) {
		$this->details = $details;

		parent::__construct(
			handler: $handler,
			handlerKey: $handlerKey,
			displayName: $displayName,
			userId: $userId,
			connectionId: $connectionId,
		);
	}
}
