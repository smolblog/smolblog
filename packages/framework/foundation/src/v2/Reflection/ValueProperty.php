<?php

namespace Smolblog\Foundation\v2\Reflection;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\v2\Utilities\StringUtils;
use Smolblog\Foundation\v2\Validation\Validated;
use Smolblog\Foundation\v2\Value;
use Smolblog\Foundation\v2\Value\CloneKit;
use Smolblog\Foundation\Value\Fields\Markdown;

/**
 * Provides information about a value's properties.
 *
 * This can be used for any number of reasons: serialization, form building, documentation generation, etc.
 */
readonly class ValueProperty implements Value, Validated {
	use CloneKit;

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
	) {
		$this->displayName = $displayName ?? StringUtils::camelToTitle($this->name);
	}

	/**
	 * Validate the object.
	 *
	 * @return void
	 */
	public function validate(): void {
		if (($this->type === 'array' || $this->type === 'map') && !isset($items)) {
			throw new InvalidValueProperties(
				message: 'Type `array` must include an `items` property.',
				field: 'items',
			);
		}
	}
}
