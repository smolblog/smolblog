<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use JsonSerializable;

/**
 * Collection objects are a specialization of the base Object that serve as a container for other Objects or Links.
 *
 * In addition to the base properties inherited by all Objects, all Collection types contain the additional properties:
 * items | totalItems | first | last | current
 *
 * The items within a Collection can be ordered or unordered. The OrderedCollection type may be used to identify a
 * Collection whose items are always ordered. In the JSON serialization, the unordered items of a Collection are
 * represented using the items property while ordered items are represented using the orderedItems property.
 */
readonly class Collection extends ActivityPubObject {
	/**
	 * Construct the object.
	 *
	 * @param string                             $id         ID (URL) for this object.
	 * @param integer|null                       $totalItems Optional total number of items in the collection.
	 * @param null|string|array|JsonSerializable ...$props   Additional properties.
	 */
	public function __construct(
		string $id,
		public ?int $totalItems = null,
		null|string|array|JsonSerializable ...$props,
	) {
		parent::__construct(...$props, id: $id);
	}
}
