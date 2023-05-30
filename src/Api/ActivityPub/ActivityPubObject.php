<?php

namespace Smolblog\Api\ActivityPub;

use Smolblog\Framework\Objects\Value;

/**
 * Base class for ActivityPub objects.
 */
abstract class ActivityPubObject extends Value {
	/**
	 * Construct the object.
	 *
	 * @param string $id   ID (usually a URI) of the object.
	 * @param string $type ActivityPub type for this object.
	 */
	public function __construct(
		public readonly string $id,
		public readonly string $type,
	) {
	}

	/**
	 * Serialize this object.
	 *
	 * @return array
	 */
	public function toArray(): array {
		return array_filter(parent::toArray(), fn($item) => isset($item));
	}
}
