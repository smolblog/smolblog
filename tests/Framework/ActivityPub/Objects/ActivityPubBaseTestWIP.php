<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use JsonSerializable;
use Smolblog\Framework\Objects\ArraySerializable;
use Smolblog\Framework\Objects\ExtendableValueKit;
use Smolblog\Framework\Objects\SerializableKit;

/**
 * Base object for ActivityPub objects.
 */
abstract readonly class ActivityPubBase implements ArraySerializable, JsonSerializable {
	use SerializableKit;
	use ExtendableValueKit;

	/**
	 * Construct the object.
	 *
	 * @param string                        $id       ID of the object.
	 * @param string|array|JsonSerializable ...$props Any additional properties.
	 */
	public function __construct(
		public string $id,
		string|array|JsonSerializable ...$props,
	) {
		$this->extendedFields = $props ?? [];
	}

	/**
	 * Get the ActivityPub Type for this object.
	 *
	 * @return string
	 */
	abstract public function type(): string|array;

	/**
	 * Get the @context attribute for this object.
	 *
	 * @return string|array
	 */
	public function context(): string|array {
		return 'https://www.w3.org/ns/activitystreams';
	}

	/**
	 * Serialize this object to an array.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$definedFields = get_object_vars($this);
		unset($definedFields['extendedFields']);

		return [
			'@context' => $this->context(),
			'type' => $this->type(),
			...$definedFields,
			...$this->extendedFields,
		];
	}
}
