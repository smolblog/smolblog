<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use JsonSerializable;

/**
 * An Activity is a subtype of Object that describes some form of action that may happen, is currently happening,
 * or has already happened. The Activity type itself serves as an abstract base type for all types of activities.
 * It is important to note that the Activity type itself does not carry any specific semantics about the kind of
 * action being taken.
 *
 * @see https://www.w3.org/TR/activitystreams-vocabulary/#dfn-activity
 */
readonly class Activity extends ActivityPubObject {
	/**
	 * Construct the object.
	 *
	 * @param string                             $id       ID of the object.
	 * @param string|Actor                       $actor    Actor performing the activity.
	 * @param string|ActivityPubObject           $object   Object the activity is being performed on.
	 * @param null|string|array|JsonSerializable ...$props Additional properties.
	 */
	public function __construct(
		string $id,
		public string|Actor $actor,
		public string|ActivityPubObject $object,
		null|string|array|JsonSerializable ...$props,
	) {
		parent::__construct(...$props, id: $id);
	}

	/**
	 * Deserialize the object.
	 *
	 * @param array $data Serialized data.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		unset($data['@context']);
		unset($data['type']);
		if (is_array($data['actor'])) {
			$data['actor'] = Actor::fromArray($data['actor']);
		}
		if (is_array($data['object'])) {
			$data['object'] = ActivityPubBase::typedObjectFromArray($data['object']);
		}
		return new static(...$data);
	}

	/**
	 * Serialize the object.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$base = parent::toArray();
		if (is_object($this->actor)) {
			$base['actor'] = $this->actor->toArray();
		}
		if (is_object($this->object)) {
			$base['object'] = $this->object->toArray();
		}

		return $base;
	}
}
