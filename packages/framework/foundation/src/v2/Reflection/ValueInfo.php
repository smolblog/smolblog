<?php

namespace Smolblog\Foundation\v2\Reflection;

use Smolblog\Foundation\v2\Fields\Markdown;
use Smolblog\Foundation\v2\Utilities\StringUtils;
use Smolblog\Foundation\v2\Value;
use Smolblog\Foundation\v2\Value\Traits\CloneKit;

/**
 * Provide information about a value type.
 */
class ValueInfo implements Value {
	use CloneKit;

	/**
	 * Type of the class, usually a fully-qualified class name.
	 *
	 * @var string
	 */
	public readonly string $type;

	/**
	 * User-friendly name.
	 *
	 * @var string
	 */
	public readonly string $displayName;

	/**
	 * Single-sentance description.
	 *
	 * @var string|null
	 */
	public readonly ?string $description;

	/**
	 * Longer description, markdown formatted.
	 *
	 * @var Markdown|null
	 */
	public readonly ?Markdown $longDescription;

	/**
	 * Information about the class' properties.
	 *
	 * @var ValueProperty[]
	 */
	public readonly array $properties;

	/**
	 * @param string               $type            Type of the class, usually a fully-qualified class name.
	 * @param string|null          $displayName     User-friendly name.
	 * @param string|null          $description     Single-sentance description.
	 * @param Markdown|string|null $longDescription Longer description, markdown formatted.
	 * @param array                $properties      Information about the class' properties.
	 */
	public function __construct(
		string $type,
		?string $displayName = null,
		?string $description = null,
		Markdown|string|null $longDescription = null,
		array $properties = [],
	) {
		$this->type = $type;
		$this->description = $description;
		$this->properties = $properties;

		$this->longDescription = match (true) {
			!isset($longDescription) => null,
			\is_a($longDescription, Markdown::class) => $longDescription,
			default => new Markdown($longDescription),
		};

		$this->displayName = $displayName ?? StringUtils::camelToTitle(StringUtils::dequalifyClassName($this->type));
	}
}
