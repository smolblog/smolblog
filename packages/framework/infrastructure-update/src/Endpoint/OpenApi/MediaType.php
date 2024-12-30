<?php

namespace Smolblog\Infrastructure\Endpoint\OpenApi;

use JsonSerializable;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Email;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Traits\ArrayType;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

readonly class MediaType extends Value implements SerializableValue {
	use SerializableValueKit;

	public function __construct(
		public Schema $schema,
		public array|JsonSerializable|string|null $example = null,
		// #[ArrayType(Encoding::class, isMap: true)] public ?array $encoding = null
	) {
	}
}
