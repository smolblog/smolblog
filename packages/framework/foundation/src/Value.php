<?php

namespace Smolblog\Foundation;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Attributes\{ArrayType, DisplayName, Target};
use Smolblog\Foundation\Value\ValueProperty;
use Throwable;

/**
 * Read-only data structure.
 *
 * Useful for passing data around from object to object. Value objects are intended to be immutable; there is a
 * metadata class available for attaching runtime data.
 *
 * Declaring `readonly` properties in a defined object allows PHP to typecheck the object instead of relying on arrays
 * with specific keys.
 */
abstract readonly class Value {
	/**
	 * Create a copy of the object with the given properties replacing existing ones.
	 *
	 * @throws InvalidValueProperties When the object cannot be copied.
	 *
	 * @param mixed ...$props Properties to override.
	 * @return static
	 */
	public function with(mixed ...$props): static {
		// Calling get_object_vars from outside context so that we only get public properties.
		// see https://stackoverflow.com/questions/13124072/ for source.
		$base = get_object_vars(...)->__invoke($this);

		try {
			// @phpstan-ignore-next-line
			return new static(...array_merge($base, $props));
		} catch (Throwable $e) {
			throw new InvalidValueProperties(
				message: 'Unable to copy Value in ' . static::class . '::with(): ' . $e->getMessage(),
				previous: $e,
			);
		}
	}

	/**
	 * Check for equality.
	 *
	 * This performs a very basic comparison; if a subclass has a more reliable method, it should override this method.
	 *
	 * @param self $other Object to compare to.
	 * @return boolean True if $this and $other are the same type with the same values.
	 */
	public function equals(self $other): bool {
		if (get_class($this) !== get_class($other)) {
			return false;
		}

		$base = get_object_vars(...)->__invoke($this);
		return $base == get_object_vars($other);
	}

	/**
	 * Get information about this class' properties.
	 *
	 * @return array<string, ValueProperty>
	 */
	public static function reflection(): array {
		$class = new ReflectionClass(static::class);
		$propReflections = $class->getProperties(ReflectionProperty::IS_PUBLIC);
		$props = [];
		foreach ($propReflections as $prop) {
			$info = static::getPropertyInfo($prop, $class);
			if (isset($info)) {
				$props[$prop->getName()] = $info;
			}
		}
		return $props;
	}

	/**
	 * Get the ValueProperty object for the given property.
	 *
	 * The individual ReflectionProperty and whole class ReflectionClass are provided to avoid re-work. To override
	 * an individual property, check `$prop->getName()`.
	 *
	 * If a field should be disculded from reflection (such as a derived property), return `null`.
	 *
	 * @throws CodePathNotSupported If a property has a union/intersection type or an array does not have an ArrayType.
	 *
	 * @param ReflectionProperty $prop  ReflectionProperty for the property being evaluated.
	 * @param ReflectionClass    $class ReflectionClass for this class.
	 * @return ValueProperty|null
	 */
	protected static function getPropertyInfo(ReflectionProperty $prop, ReflectionClass $class): ?ValueProperty {
		$type = $prop->getType();
		if (!isset($type) || get_class($type) !== ReflectionNamedType::class) {
			throw new CodePathNotSupported(
				message: 'Union/intersection types are not supported; ' .
					'change the type or override the getPropertyInfo() method.',
				location: 'Value::getPropertyInfo via' . static::class,
			);
		}

		$params = [
			'name' => $prop->getName(),
			'type' => $type->getName(),
		];

		if ($params['type'] === 'array') {
			$attributeReflections = $prop->getAttributes(ArrayType::class, ReflectionAttribute::IS_INSTANCEOF);
			$arrayType = ($attributeReflections[0] ?? null)?->newInstance() ?? null;
			if (!isset($arrayType)) {
				throw new CodePathNotSupported(
					message: 'Arrays must have an ArrayType attribute; ' .
						'add the attribute or override the getPropertyInfo() method.',
					location: 'Value::getPropertyInfo via' . static::class,
				);
			}

			if ($arrayType->isMap) {
				$params['type'] = 'map';
			}

			if ($arrayType->type !== ArrayType::NO_TYPE) {
				$params['items'] = $arrayType->type;
			}
		}

		$targetReflection = $prop->getAttributes(Target::class, ReflectionAttribute::IS_INSTANCEOF);
		$target = ($targetReflection[0] ?? null)?->newInstance() ?? null;
		if ($target) {
			$params['target'] = $target->type;
		}

		$nameReflection = $prop->getAttributes(DisplayName::class, ReflectionAttribute::IS_INSTANCEOF);
		$displayName = ($nameReflection[0] ?? null)?->newInstance() ?? null;
		if ($displayName) {
			$params['displayName'] = $displayName->name;
		}

		return new ValueProperty(...$params);
	}
}
