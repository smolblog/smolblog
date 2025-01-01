<?php

namespace Smolblog\Infrastructure\OpenApi;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * Get the information needed to add an Endpoint to an OpenAPI document.
 */
readonly class OpenApiObjectSchema extends Value implements SerializableValue {
	use SerializableValueKit;

	/**
	 * Create the object.
	 *
	 * @see https://spec.openapis.org/oas/latest.html#schema-object
	 *
	 * @param array                    $schema            OpenAPI-compliant schema definition.
	 * @param array<int, class-string> $referencedClasses Any classes referenced in $schema that should be added.
	 */
	public function __construct(
		public array $schema,
		public array $referencedClasses = [],
	) {
	}
}
