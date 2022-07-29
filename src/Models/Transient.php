<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Model;

/**
 * An object that needs to persist between pageloads but is not permanent.
 */
class Transient extends Model {
	/**
	 * Fields available for a Transient.
	 *
	 * @var array
	 */
	protected array $fields = [
		'key',
		'value',
	];

	/**
	 * Validate incoming data.
	 *
	 * @param string $name  Property to set.
	 * @param mixed  $value Value to set.
	 * @return string|null null if valid, error message if not
	 */
	protected function fieldValidationErrorMessage(string $name, mixed $value): string {
		switch ($name) {
			case 'key':
				try {
					strval($value);
				} catch (Throwable $e) {
					// If there is an exception raised during `strval`, then it won't convert.
					return "$name must be stringable.";
				}
				return null;
			case 'value':
				return null;
		}
		return "$name is not a field.";
	}
}
