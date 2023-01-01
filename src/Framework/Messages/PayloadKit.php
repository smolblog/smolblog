<?php

namespace Smolblog\Framework\Messages;

use DateTimeInterface;

/**
 * For Event groups that have a base event with subclasses.
 *
 * This provides a set of functions for the base event class that assist with serializing common and uncommon
 * information. Fields that are unique to a subclass can (un)serialize into a 'payload' array. This can allow an event
 * store to index common information without needing to be aware of every single permutation.
 */
trait PayloadKit {
	/**
	 * For subclasses to provide any additional fields in a serialized array format.
	 *
	 * In this format so that the additional fields can be type-checked by the subclasses but still serialized and
	 * stored in a standard format.
	 *
	 * @return array
	 */
	abstract public function getPayload(): array;

	/**
	 * Get the properties defined on this class as a serialized array.
	 *
	 * @return array
	 */
	abstract private function getStandardProperties(): array;

	/**
	 * Gets unserizlized parameters from a serialized payload.
	 *
	 * So that subclasses can unserizalize individual pararameters within a payload (for example, turning UUID strings
	 * back into Identifier objects).
	 *
	 * @param array $payload Array to deserialize.
	 * @return array
	 */
	abstract protected static function payloadFromArray(array $payload): array;

	/**
	 * Create a subclass of this Event from an array with a 'type' key.
	 *
	 * @param array $data Serialized Event.
	 * @return mixed Instance of Event indicated by $data['type']
	 */
	public static function fromTypedArray(array $data): mixed {
		$type = $data['type'];
		$payload = $data['payload'];
		$properties = $data;
		unset($properties['type']);
		unset($properties['payload']);

		return new $type(...[...$properties, ...$type::payloadFromArray($payload)]);
	}

	/**
	 * Convert this event into a serialized array with 'type' and 'payload' properties.
	 *
	 * @return array
	 */
	public function toArray(): array {
		return [
			'type' => static::class,
			...$this->getStandardProperties(),
			'payload' => $this->getPayload(),
		];
	}
}
