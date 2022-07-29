<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Model;

/**
 * Stores information about credentials needed to authenticate against an
 * exteral API as a particular user.
 */
class ConnectionCredential extends Model {
	/**
	 * Fields available for an ConnectionCredential.
	 *
	 * @var array
	 */
	protected array $fields = [
		'id',
		'user_id',
		'provider',
		'provider_key',
		'display_name',
		'details',
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
			case 'id':
			case 'user_id':
				if (!is_int($value)) {
					return "$name is an integer.";
				}
				return null;
			case 'provider':
			case 'provider_key':
			case 'display_name':
				try {
					strval($value);
				} catch (Throwable $e) {
					return "$name must be stringable.";
				}
				return null;
			case 'details':
				return null;
		}
		return "$name is not a field.";
	}
}
