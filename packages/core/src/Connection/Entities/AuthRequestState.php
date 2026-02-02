<?php

namespace Smolblog\Core\Connection\Entities;

use Cavatappi\Foundation\Reflection\MapType;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * State for an OAuth request. Not an Entity because, though it needs to persist, it doesn't need the extra
 * requirements of being an Entity. It can be persisted in any key-value store.
 */
readonly class AuthRequestState implements Value {
	use ValueKit;

	/**
	 * Create the state
	 *
	 * @param string        $key         String used by both parties to identify the request.
	 * @param UuidInterface $userId      User this request is attached to.
	 * @param string        $handler     Connector this request is using.
	 * @param array         $info        Information to store with this request.
	 * @param string        $returnToUrl Optional URL to return the user to after completion.
	 */
	public function __construct(
		public readonly string $key,
		public readonly UuidInterface $userId,
		public readonly string $handler,
		#[MapType('mixed')] public readonly array $info,
		public readonly ?string $returnToUrl = null,
	) {}
}
