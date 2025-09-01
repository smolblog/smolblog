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

	/**
	 * Array to store ad-hoc fields.
	 *
	 * @var array
	 */
	private array $extendedFields;

	/**
	 * Construct the object.
	 *
	 * @param string                             $id       ID of the object.
	 * @param null|string|array|JsonSerializable ...$props Any additional properties.
	 */
	public function __construct(
		public string $id,
		null|string|array|JsonSerializable ...$props,
	) {
		$this->extendedFields = $props ?? [];
	}

	/**
	 * Get the ActivityPub Type for this object.
	 *
	 * @return string
	 */
	public function type(): string|array {
		return substr(static::class, strrpos(static::class, '\\') + 1);
	}

	/**
	 * Get the @context attribute for this object.
	 *
	 * @return string|array
	 */
	public function context(): string|array {
		return 'https://www.w3.org/ns/activitystreams';
	}

	/**
	 * Quick access for any added variables.
	 *
	 * @param string $name Variable to get.
	 * @return mixed Value of the variable or null if not found.
	 */
	public function __get(string $name): mixed {
		return $this->extendedFields[$name] ?? null;
	}

	/**
	 * Serialize this object to an array.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$definedFields = get_object_vars($this);
		unset($definedFields['extendedFields']);

		return array_filter([
			'@context' => $this->context(),
			'type' => $this->type(),
			...$definedFields,
			...$this->extendedFields,
		], fn($i) => isset($i));
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

		return new static(...$data);
	}

	/**
	 * Deserialize an ActivityPub object from an unknown array.
	 *
	 * @param array $data Serialized unknown ActivityPub object.
	 * @return ActivityPubBase|null
	 */
	public static function typedObjectFromArray(array $data): ?ActivityPubBase {
		$givenType = ucfirst(strtolower($data['type'] ?? ''));
		unset($data['@context']);
		unset($data['type']);

		$potentialClass = __NAMESPACE__ . "\\$givenType";

		return match (true) {
			$givenType === 'Object' => ActivityPubObject::fromArray($data),
			// Check if this is a type of Actor.
			ActorType::tryFrom($givenType) !== null => Actor::fromArray([...$data, 'type' => $givenType]),
			// Check for a class with this type.
			class_exists($potentialClass) => $potentialClass::fromArray($data),
			default => null,
		};
	}
}
