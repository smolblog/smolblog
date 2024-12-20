<?php

namespace Smolblog\Infrastructure\Endpoint\OpenApi;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\ArrayType;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * Root object describing an OpenAPI spec.
 *
 * @see https://learn.openapis.org/specification/
 */
readonly class OpenApiSpec extends Value implements SerializableValue {
	use SerializableValueKit;

	public function __construct(
		public string $openapi,
		public object $info,
		#[ArrayType(ArrayType::TYPE_ARRAY)] public ?array $servers,
		public ?object $paths,
		#[ArrayType(ArrayType::TYPE_ARRAY)] public ?array $webhooks,
		public ?object $components,
		public ?object $security,
		#[ArrayType(ArrayType::TYPE_ARRAY)] public ?array $tags,
		public ?object $externalDocs,
	) {
	}
}
