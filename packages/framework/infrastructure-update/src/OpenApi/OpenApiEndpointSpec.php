<?php

namespace Smolblog\Infrastructure\OpenApi;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * Get the information needed to add an Endpoint to an OpenAPI document.
 */
readonly class OpenApiEndpointSpec extends Value implements SerializableValue {
	use SerializableValueKit;

	/**
	 * Create the object.
	 *
	 * @see https://spec.openapis.org/oas/latest.html#operation-object
	 *
	 * @param array          $operation         OpenAPI-compliant operation definition.
	 * @param class-string[] $referencedClasses Any classes referenced in $operation that should be added.
	 */
	public function __construct(
		public array $operation,
		public array $referencedClasses = [],
	) {
	}
}
