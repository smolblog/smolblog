<?php

namespace Smolblog\Core\Connection\Entities;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * State for an OAuth request. Not an Entity because, though it needs to persist, it doesn't need the extra
 * requirements of being an Entity. It can be persisted in any key-value store.
 */
readonly class AuthRequestState extends Value implements SerializableValue {
	use SerializableValueKit;

	/**
	 * Create the state
	 *
	 * @param string     $key         String used by both parties to identify the request.
	 * @param Identifier $userId      User this request is attached to.
	 * @param string     $handler     Connector this request is using.
	 * @param array      $info        Information to store with this request.
	 * @param string     $returnToUrl Optional URL to return the user to after completion.
	 */
	public function __construct(
		public readonly string $key,
		public readonly Identifier $userId,
		public readonly string $handler,
		public readonly array $info,
		public readonly ?string $returnToUrl = null,
	) {
	}
}
