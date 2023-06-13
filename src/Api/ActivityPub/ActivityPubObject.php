<?php

namespace Smolblog\Api\ActivityPub;

use Smolblog\Framework\Objects\ExtendableValueKit;
use Smolblog\Framework\Objects\Value;

/**
 * Base class for ActivityPub objects.
 */
abstract class ActivityPubObject extends Value {
	use ExtendableValueKit;

	/**
	 * Construct the object.
	 *
	 * Extended fields are provided to comply with the directive that servers should ignore unrecognized fields.
	 *
	 * @param string $id          ID (usually a URI) of the object.
	 * @param string $type        ActivityPub type for this object.
	 * @param mixed  ...$etCetera Additional fields found on the object.
	 */
	public function __construct(
		public readonly string $id,
		public readonly string $type,
		mixed ...$etCetera
	) {
		$this->extendedFields = $etCetera;
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
