<?php

namespace Smolblog\Framework\Messages;

use DateTimeInterface;
use Smolblog\Framework\Objects\SerializableKit;

/**
 * For Event groups that have a base event with subclasses.
 *
 * This provides a set of functions for the base event class that assist with serializing common and uncommon
 * information. Fields that are unique to a subclass can (un)serialize into a 'payload' array. This can allow an event
 * store to index common information without needing to be aware of every single permutation.
 */
trait PayloadKit {
	use SerializableKit;

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
	 * back into Identifier objects). If not overridden, returns the payload unchanged (for when the payload only
	 * contains primitive values).
	 *
	 * @param array $payload Array to deserialize.
	 * @return array
	 */
	protected static function payloadFromArray(array $payload): array {
		return $payload;
	}

	/**
	 * Gets unserizlized parameters from serialized standard properties.
	 *
	 * So that classes can unserizalize individual pararameters within a payload (for example, turning UUID strings
	 * back into Identifier objects). If not overridden, returns the properties unchanged (for when the properties
	 * only contain primitive values).
	 *
	 * @param array $properties Array to deserialize.
	 * @return array
	 */
	protected static function standardPropertiesFromArray(array $properties): array {
		return $properties;
	}

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
		$defaultProperties = [];

		$id = self::safeDeserializeIdentifier($data['id'] ?? '');
		if (isset($id)) {
			$defaultProperties['id'] = $id;
			unset($properties['id']);
		}

		$timestamp = self::safeDeserializeDate($data['timestamp'] ?? '');
		if (isset($timestamp)) {
			$defaultProperties['timestamp'] = $timestamp;
			unset($properties['timestamp']);
		}

		unset($properties['type']);
		unset($properties['payload']);

		return new $type(...[
			...$defaultProperties,
			...static::standardPropertiesFromArray($properties),
			...$type::payloadFromArray($payload)
		]);
	}

	/**
	 * Convert this event into a serialized array with 'type' and 'payload' properties.
	 *
	 * Automatically adds the `id` and `timestamp` properties if they exist on the object. As this trait is intended
	 * for Event objects, these properties are expected. If they are not present, they will not be included and the
	 * function will not fail.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$defaultEventProperties = [
			'id' => $this->id?->toString() ?? null,
			'timestamp' => $this->timestamp?->format(DateTimeInterface::RFC3339_EXTENDED) ?? null,
		];
		return array_filter([
			'type' => static::class,
			...$defaultEventProperties,
			...$this->getStandardProperties(),
			'payload' => array_filter($this->getPayload()),
		]);
	}
}
