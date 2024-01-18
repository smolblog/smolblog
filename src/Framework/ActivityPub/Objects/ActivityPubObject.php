<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use JsonSerializable;

/**
 * Base object for ActivityPub objects.
 */
readonly class ActivityPubObject extends ActivityPubBase {
	/**
	 * Construct the object.
	 *
	 * @param string                             $id       ID of the object.
	 * @param null|string|array|JsonSerializable ...$props Any additional properties.
	 */
	public function __construct(
		string $id,
		null|string|array|JsonSerializable ...$props,
	) {
		parent::__construct(...$props, id: $id);
	}

	/**
	 * Get the ActivityPub Type for this object.
	 *
	 * @return string
	 */
	public function type(): string {
		// Only modify if this is an actual ActivityPubObject instance.
		return self::class === static::class ? 'Object' : parent::type();
	}
}
