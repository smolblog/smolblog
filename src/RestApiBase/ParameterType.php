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
	 * Declare an object parameter with the given properties.
	 *
	 * @param ParameterType[] $properties Associative array of properties.
	 * @return ParameterType
	 */
	public static function object(array $properties): ParameterType {
		return new ParameterType(type: 'object', properties: $properties);
	}

	/**
	 * Make the given type required.
	 *
	 * @param ParameterType $base ParameterType that should be required.
	 * @return ParameterType
	 */
	public static function required(ParameterType $base): ParameterType {
		return ParameterType::fromArray([...$base->toArray(), 'required' => true]);
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
		if ($this->type === 'dateTime') {
			$this->type = 'date-time';
		}
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

		if ($this->type === 'object') {
			$base['properties'] = array_map(fn($p) => $p->schema(), $this->properties);
			$base['required'] = array_keys(array_filter($this->properties, fn($p) => $p->required));
		}

		return array_filter($base, fn($i) => isset($i));
	}
}
