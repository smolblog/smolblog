<?php

namespace Smolblog\Foundation\Value\Traits;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;

/**
 * Default serializing functions.
 */
trait SerializableValueKit {
	/**
	 * Serialize the object to an array. This can be used to serialize to JSON.
	 *
	 * Return type is `mixed` to allow for scalar values in subclasses.
	 *
	 * @return mixed
	 */
	public function toArray(): mixed {
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

			if (is_array($this->$name)) {
				$data[$name] = isset($type) ? array_map(fn($item) => $item->toArray(), $this->$name) : $this->$name;
				continue;
			}

			if (is_object($this->$name)) {
				$data[$name] = $this->$name->toArray();
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
	public static function fromArray(array $data): static {
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
				$parsedData[$name] = array_map(fn($item) => ($type->type)::fromArray($item), $data[$name]);
				continue;
			}

			$parsedData[$name] = $type::fromArray($data[$name]);
		}

		return new static(...$parsedData);
	}

	/**
	 * Serialize the object.
	 *
	 * @return mixed
	 */
	public function jsonSerialize(): mixed {
		return $this->toArray();
	}

	/**
	 * Serialize the object to JSON.
	 *
	 * Mostly provided as a symmetry to to/fromArray.
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
		return static::fromArray($data);
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
