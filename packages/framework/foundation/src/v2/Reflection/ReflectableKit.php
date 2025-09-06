<?php

namespace Smolblog\Foundation\v2\Reflection;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Value\Attributes\DisplayName;
use Smolblog\Foundation\Value\Attributes\Target;

use function get_class;

trait ReflectableKit {
	/**
	 * Get information about this class' properties.
	 *
	 * @return array<string, ValueProperty>
	 */
	public static function reflect(): array {
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
		if (!isset($type) || \get_class($type) !== ReflectionNamedType::class) {
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
					message: 'Arrays must have either a ListType or MapType attribute; ' .
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
