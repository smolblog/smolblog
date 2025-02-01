<?php

namespace Smolblog\Foundation\Value\Traits;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Throwable;

trait SerializableSupertypeBackupKit {
	/**
	 * Get the field that all additional (not explicitly defined) fields should be stored in.
	 *
	 * @return string
	 */
	abstract private static function extraPropsField(): string;

	/**
	 * Deserialize the object. Any unknown fields are put in the `extraPropsField` array.
	 *
	 * @throws InvalidValueProperties Thrown if the object could not be deserialized.
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function deserializeValue(array $data): static {
		$extraField = self::extraPropsField();

		if (isset($data[$extraField])) {
			return self::baseDeserialize($data);
		}

		$modified = array_filter(
			$data,
			fn($key) => in_array($key, array_keys(static::propertyInfo())),
			ARRAY_FILTER_USE_KEY
		);
		$modified[$extraField] = array_diff_key($data, $modified);

		try {
			return self::baseDeserialize($modified);
		} catch (Throwable $e) {
			throw new InvalidValueProperties(
				message: 'Could not deserialize to ' . parent::class,
				previous: $e
			);
		}
	}
}
