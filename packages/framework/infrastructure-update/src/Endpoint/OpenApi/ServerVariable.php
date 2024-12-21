<?php

namespace Smolblog\Infrastructure\Endpoint\OpenApi;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Email;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Traits\ArrayType;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

readonly class ServerVariable extends Value implements SerializableValue {
	use SerializableValueKit;

	public function __construct(
		public string $default,
		#[ArrayType(ArrayType::TYPE_STRING)] public ?array $enum = null,
		public ?Markdown $description = null,
	) {
		if (empty($enum)) {
			$this->enum = null;
		} else {
			if (!in_array($default, $enum)) {
				throw new InvalidValueProperties("Default value $default not found in enum array.");
			}
		}
	}
}
