<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Entity\Entity;

/**
 * State for an OAuth request. Needs to be persistent between requests, so
 * it's an Entity.
 */
class ConnectionCreationState extends Entity {
	/**
	 * Create the state
	 *
	 * @param integer|string $id     String used by both parties to identify the request.
	 * @param integer        $userId User this request is attached to.
	 * @param array          $info   Information to store with this request.
	 */
	public function __construct(
		public readonly int|string $id,
		public readonly int $userId,
		public readonly array $info,
	) {
	}
}
