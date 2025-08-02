<?php

namespace Smolblog\Foundation\Value;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Foundation\Value\Traits\ArrayType;

/**
 * Provides information about a value's properties.
 *
 * This can be used for any number of reasons: serialization, form building, documentation generation, etc.
 */
readonly class ValueProperty extends Value {
	/**
	 * Human-readable display name.
	 *
	 * @var string
	 */
	public readonly string $displayName;

	/**
	 * Create the property.
	 *
	 * @throws InvalidValueProperties If requiredinformation is not provided or nonsensical.
	 *
	 * @param string                   $name            Name of the property.
	 * @param class-string|string      $type            Basic type or fully-qualified class name.
	 * @param class-string|string|null $items           Additional type information. Required for arrays.
	 * @param class-string|string|null $target          For Identifiers or other pointers.
	 * @param string|null              $displayName     Human-readable display name. Default generated from name.
	 * @param string|null              $description     Optional single-line description.
	 * @param Markdown|null            $longDescription Optional Markdown-formatted longer description.
	 * @param boolean                  $optional        If the property can be `null`. Default true.
	 * @param mixed                    $default         Default value for the property if any.
	 * @param integer|null             $min             Optional minimum value.
	 * @param integer|null             $max             Optional maximum value.
	 * @param string|null              $pattern         Optional regular expression to validate against.
	 */
	public function __construct(
		public string $name,
		public string $type,
		public ?string $items = null,
		public ?string $target = null,
		?string $displayName = null,
		public ?string $description = null,
		public ?Markdown $longDescription = null,
		public bool $optional = true,
		public mixed $default = null,
		public ?int $min = null,
		public ?int $max = null,
		public ?string $pattern = null,
	) {
		if ($type === 'array' && !isset($items)) {
			throw new InvalidValueProperties(
				message: 'Type `array` must include an `items` property.',
				field: 'items',
			);
		}

		if (isset($min, $max) && $min > $max) {
			throw new InvalidValueProperties(
				message: 'Nonsensical constraints: `min` is greater than `max`',
				field: 'min',
			);
		}

		// Via https://stackoverflow.com/a/42665007.
		$this->displayName = $displayName ??
			ucwords(implode(' ', preg_split('/(?=[A-Z])/', $this->name) ?: []));
	}
}
