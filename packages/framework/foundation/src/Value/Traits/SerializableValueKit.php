<?php

namespace Smolblog\Foundation\Value\Traits;

use BackedEnum;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\ValueProperty;
use Throwable;

/**
 * Default serializing functions.
 */
trait SerializableValueKit {
	/**
	 * Compare serialized values to determine equality.
	 *
	 * @param Value $other Object to compare to.
	 * @return boolean
	 */
	public function equals(Value $other): bool {
		return is_a($other, SerializableValue::class) && json_encode($this) == json_encode($other);
	}

	/**
	 * Serialize the object to an array. This can be used to serialize to JSON.
	 *
	 * Return type is `mixed` to allow for scalar values in subclasses.
	 *
	 * @throws CodePathNotSupported If a property is not a SerializableValue.
	 *
	 * @return mixed
	 */
	public function serializeValue(): mixed {
		$props = static::propertyInfo();
		$data = [];
		foreach ($props as $name => $type) {
			if (!isset($this->$name)) {
				continue;
			}

			if (!isset($type)) {
				$data[$name] = $this->$name;
				continue;
			}

			$data[$name] = $this->serializeProperty($this->$name);
		}//end foreach
		return $data;
	}

	/**
	 * Deserialize the object from an array (typically JSON).
	 *
	 * @throws InvalidValueProperties If the object cannot be deserialized.
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function deserializeValue(array $data): static {
		$parsedData = [];
		$props = static::propertyInfo();

		foreach ($props as $name => $type) {
			if (!isset($data[$name])) {
				continue;
			}

			if (!isset($type)) {
				$parsedData[$name] = $data[$name] ?? null;
				continue;
			}

			if (is_a($type, ArrayType::class)) {
				$parsedData[$name] = array_map(
					fn($item) => self::deserializeDataToType($item, $type->type),
					$data[$name]
				);
				continue;
			}

			$parsedData[$name] = self::deserializeDataToType($data[$name], $type);
		}//end foreach

		// @phpstan-ignore-next-line
		return new static(...$parsedData);
	}

	/**
	 * Serialize the object.
	 *
	 * @return mixed
	 */
	public function jsonSerialize(): mixed {
		return $this->serializeValue();
	}

	/**
	 * Serialize the object to JSON.
	 *
	 * Mostly provided as a symmetry to to/deserializeValue.
	 *
	 * @return string
	 */
	public function toJson(): string {
		return json_encode($this);
	}

	/**
	 * Deserialize the object.
	 *
	 * @param string $json Serialized object.
	 * @return static
	 */
	public static function fromJson(string $json): static {
		$data = json_decode($json, true);
		return static::deserializeValue($data);
	}

	/**
	 * Get the properties that should be serialized and their class if they are objects or arrays of objects.
	 *
	 * @return array
	 */
	protected static function propertyInfo(): array {
		return array_map(
			function (ValueProperty $prop) {
				if ($prop->type === 'array' && isset($prop->items)) {
					$arrayType = new ArrayType(type: $prop->items);
					return ($arrayType->isBuiltIn() || $arrayType->type === ArrayType::NO_TYPE) ? null : $arrayType;
				}

				return class_exists($prop->type) ? $prop->type : null;
			},
			static::reflection(),
		);
	}

	/**
	 * Serialize the given property.
	 *
	 * Checks for a BackedEnum, then recursively maps an array, then checks for a SerializableValue.
	 *
	 * @throws CodePathNotSupported If a property is an object but not a SerializableValue.
	 *
	 * @param mixed $value Value to serialize.
	 * @return mixed Serialized $value.
	 */
	private function serializeProperty(mixed $value): mixed {
		if (is_a($value, BackedEnum::class)) {
			return $value->value;
		}

		if (is_array($value)) {
			return array_map(fn($item) => $this->serializeProperty($item), $value);
		}

		if (is_object($value) && is_a($value, SerializableValue::class)) {
			return $value->serializeValue();
		}

		throw new CodePathNotSupported(
			message: get_class($value) . ' is not a SerializableValue. ' .
				'Change the type or override serializeValue()',
			location: 'SerializableValueKit::serializeValue via ' . static::class
		);
	}

	/**
	 * Check the class to deserialize has a known interface and exists.
	 *
	 * @throws CodePathNotSupported If $type is unknown or not deserializable.
	 *
	 * @param mixed  $data Data to deserialize.
	 * @param string $type Type to deserialize to.
	 * @return mixed Deserialized object of type $type or unmodified $data.
	 */
	private static function deserializeDataToType(mixed $data, string $type): mixed {
		// If $type isn't a BackedEnum or SerializableValue, don't touch.
		if (class_exists($type)) {
			if (is_subclass_of($type, BackedEnum::class, allow_string: true)) {
				return $type::from($data);
			}
			if (is_subclass_of($type, SerializableValue::class, allow_string: true)) {
				return $type::deserializeValue($data);
			}
		}

		throw new CodePathNotSupported(
			message: "$type is not a SerializableValue. Change the type or override deserializeValue()",
			location: 'SerializableValueKit::deserializeValue via ' . static::class
		);
	}
}
