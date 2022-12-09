<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Framework\Entity;
use Smolblog\Framework\Identifier;

/**
 * State for an OAuth request. Needs to be persistent between requests, so
 * it's an Entity.
 */
readonly class AuthRequestState extends Entity {
	public const NAMESPACE = 'ff5fc3f5-d807-4bc8-b3a9-58efdbc4bd8e';

	/**
	 * Consistently build a unique identifier out of the key.
	 *
	 * @param string $key String used by both parties to identify the request.
	 * @return Identifier ID constructed from connection and key.
	 */
	public static function buildId(string $key): Identifier {
		return Identifier::createFromName(namespace: self::NAMESPACE, name: $key);
	}

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
		parent::__construct(id: self::buildId(key: $key));
	}
}
