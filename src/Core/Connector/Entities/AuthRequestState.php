<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Framework\Objects\Value;

/**
 * State for an OAuth request. Not an Entity because, though it needs to persist, it doesn't need the extra
 * requirements of being an Entity. It can be persisted in any key-value store.
 */
class AuthRequestState extends Value {
	/**
	 * Create the state
	 *
	 * @param string  $key    String used by both parties to identify the request.
	 * @param integer $userId User this request is attached to.
	 * @param array   $info   Information to store with this request.
	 */
	public function __construct(
		public readonly string $key,
		public readonly int $userId,
		public readonly array $info,
	) {
	}
}
