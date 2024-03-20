<?php

namespace Smolblog\Framework\Foundation;

use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;
use ReflectionNamedType;
use ReflectionAttribute;
use Smolblog\Framework\Foundation\Attributes\ArrayType;
use Smolblog\Framework\Foundation\Exceptions\CodePathNotSupported;

/**
 * Read-only data structure.
 *
 * Useful for passing data around from object to object. Value objects are intended to be immutable; there is a
 * metadata class available for attaching runtime data.
 *
 * Declaring `readonly` properties in a defined object allows PHP to typecheck the object instead of relying on arrays
 * with specific keys.
 */
abstract readonly class Value implements JsonSerializable {
	/**
	 * Serialize the object to an array. This can be used to serialize to JSON.
	 *
	 * @return mixed
	 */
	public function serialize(): mixed {
		$props = static::propertyInfo();
		$data = [];
		foreach ($props as $name => $type) {
			if (!isset($type)) {
				$data[$name] = $this->$name;
				continue;
			}

			if (is_array($this->$name)) {
				$data[$name] = isset($type) ? array_map(fn($item) => $item->serialize(), $this->$name) : $this->$name;
				continue;
			}

			if (is_object($this->$name)) {
				$data[$name] = $this->$name->serialize();
				continue;
			}
		}
		return $data;
	}

	/**
	 * Deserialize the object from an array (typically JSON).
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function deserialize(array $data): static {
		$parsedData = [];
		$props = static::propertyInfo();

		foreach ($props as $name => $type) {
			if (!isset($type)) {
				$parsedData[$name] = $data[$name] ?? null;
				continue;
			}

			if (is_a($type, ArrayType::class)) {
				$parsedData[$name] = array_map(fn($item) => ($type->type)::deserialize($item), $data[$name]);
				continue;
			}

			$parsedData[$name] = $type::deserialize($data[$name]);
		}

		return new static(...$parsedData);
	}

	/**
	 * Serialize the object.
	 *
	 * @return mixed
	 */
	public function jsonSerialize(): mixed {
		return $this->serialize();
	}

	/**
	 * Deserialize the object.
	 *
	 * @param string $json Serialized object.
	 * @return static
	 */
	public static function jsonDeserialize(string $json): static {
		$data = json_decode($json, true);
		return static::deserialize($data);
	}

	/**
	 * Get the properties that should be serialized and their class if they are objects or arrays of objects.
	 *
	 * @return array
	 */
	protected static function propertyInfo(): array {
		$propReflections = (new ReflectionClass(static::class))->getProperties(ReflectionProperty::IS_PUBLIC);
		$props = [];
		foreach ($propReflections as $prop) {
			$props[$prop->getName()] = self::determinePropertyType($prop);
		}
		return $props;
	}

	/**
	 * Determine the type of a property if it is an object or array of objects. Will return null if the property is
	 * a built-in type.
	 *
	 * @throws CodePathNotSupported If the property is a union/intersection type.
	 *
	 * @param ReflectionProperty $prop Property to check.
	 * @return string|ArrayType|null
	 */
	private static function determinePropertyType(ReflectionProperty $prop): string|ArrayType|null {
		$type = $prop->getType();
		if (get_class($type) !== ReflectionNamedType::class) {
			throw new CodePathNotSupported(
				message: 'Union/intersection types are not supported; ' .
					'change the type or override the propertyInfo() method.',
				location: 'Value::determinePropertyType via' . static::class,
			);
		}

		$typeName = $type->getName();
		if ($type->isBuiltin() && $typeName !== 'array') {
			return null;
		}

		if ($typeName === 'array') {
			$attributeReflections = $prop->getAttributes(ArrayType::class, ReflectionAttribute::IS_INSTANCEOF);
			return ($attributeReflections[0] ?? null)?->newInstance() ?? null;
		}

		return class_exists($typeName) ? $typeName : null;
	}
}
