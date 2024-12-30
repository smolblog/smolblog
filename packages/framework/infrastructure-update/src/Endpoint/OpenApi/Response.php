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

readonly class Response extends Value implements SerializableValue {
	use SerializableValueKit;

	public function __construct(
		public Markdown $description,
		#[ArrayType(Header::class, isMap: true)] public ?array $headers = null,
		#[ArrayType(MediaType::class, isMap: true)] public ?array $content = null,
	) {
	}
}
