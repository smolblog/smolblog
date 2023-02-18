<?php

namespace Smolblog\RestApiBase;

use Attribute;
use Smolblog\Framework\Objects\ExtendableValueKit;

/**
 * Class to declare and define API parameters.
 */
#[Attribute]
class ParameterType {
	use ExtendableValueKit;

	/**
	 * Declare a string parameter
	 *
	 * @param string|null $format  Optional format.
	 * @param string|null $pattern Optional regular expression to validate the parameter.
	 * @return ParameterType
	 */
	public static function string(?string $format = null, ?string $pattern = null): ParameterType {
		return new ParameterType(type: 'string', format: $format, pattern: $pattern);
	}

	/**
	 * Declare an Identifier (UUID) parameter
	 *
	 * @return ParameterType
	 */
	public static function identifier(): ParameterType {
		return self::string(
			format: 'uuid',
			pattern: '^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9][0-9a-fA-F]{3}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$'
		);
	}

	/**
	 * Declare a DateTime parameter
	 *
	 * @return ParameterType
	 */
	public static function dateTime(): ParameterType {
		return self::string(format: 'date-time');
	}

	/**
	 * Declare a Date parameter
	 *
	 * @return ParameterType
	 */
	public static function date(): ParameterType {
		return self::string(format: 'date');
	}

	/**
	 * Declare a Number parameter
	 *
	 * @return ParameterType
	 */
	public static function number(): ParameterType {
		return new ParameterType(type: 'number');
	}

	/**
	 * Declare a Integer parameter
	 *
	 * @return ParameterType
	 */
	public static function integer(): ParameterType {
		return new ParameterType(type: 'integer');
	}

	/**
	 * Declare a Boolean parameter
	 *
	 * @return ParameterType
	 */
	public static function boolean(): ParameterType {
		return new ParameterType(type: 'boolean');
	}

	/**
	 * Declare an Array parameter
	 *
	 * The items paramter defines the type of the objects inside the array. Mixed arrays are not currently supported.
	 *
	 * @param ParameterType $items The parameter type that makes up the array.
	 * @return ParameterType
	 */
	public static function array(ParameterType $items): ParameterType {
		return new ParameterType(type: 'array', items: $items);
	}

	/**
	 * Declare an object parameter with a class.
	 *
	 * $class must be a fully-qualified class name that can be serialized to and from JSON.
	 *
	 * @param string $class Class name that defines the parameter.
	 * @return ParameterType
	 */
	public static function object(string $class): ParameterType {
		return new ParameterType(type: 'object', backingClass: $class);
	}

	/**
	 * Construct the type
	 *
	 * @param string  $type     OpenAPI type.
	 * @param boolean $required True if this is a required parameter.
	 * @param mixed   ...$props Any additional properties.
	 */
	public function __construct(
		public readonly string $type,
		public readonly bool $required = false,
		mixed ...$props,
	) {
		$this->extendedFields = $props;
	}

	/**
	 * Get the OpenAPI-compatible schema for this type.
	 *
	 * @return array
	 */
	public function schema(): array {
		$base = $this->toArray();
		unset($base['required']);

		return array_filter($base, fn($i) => isset($i));
	}
}
