<?php

namespace Smolblog\Framework\Objects;

/**
 * A shim trait to translate ArraySerializable methods into SerializableValue methods.
 *
 * @deprecated
 */
trait SerializableValueShim {
	/**
	 * Translate to serializeValue
	 *
	 * @return array
	 * @deprecated
	 */
	public function toArray(): array {
		return $this->serializeValue();
	}

	/**
	 * Translate to ::deserializeValue()
	 *
	 * @param array $data Serialized data.
	 * @return static
	 * @deprecated
	 */
	public static function fromArray(array $data): static {
		return static::deserializeValue();
	}

	/**
	 * Translate to ::fromJson
	 *
	 * @param string $json JSON to decode.
	 * @return static
	 *
	 * @deprecated
	 */
	public static function jsonDeserialize(string $json): static {
		return static::fromJson($json);
	}
}
