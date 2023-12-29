<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use JsonSerializable;
use Smolblog\Framework\Objects\ArraySerializable;
use Smolblog\Framework\Objects\ExtendableValueKit;
use Smolblog\Framework\Objects\SerializableKit;

/**
 * Base object for ActivityPub objects.
 */
abstract readonly class ActivityPubObject extends ActivityPubBase {
	/**
	 * Construct the object.
	 *
	 * @param string                        $id       ID of the object.
	 * @param string|array|JsonSerializable ...$props Any additional properties.
	 */
	public function __construct(
		string $id,
		string|array|JsonSerializable ...$props,
	) {
		parent::__construct(...$props, id: $id);
	}

	/**
	 * Get the ActivityPub Type for this object.
	 *
	 * @return string
	 */
	public function type(): string {
		return 'Object';
	}
}
