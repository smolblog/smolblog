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

readonly class Parameter extends Value implements SerializableValue {
	use SerializableValueKit;

	public function __construct(
		public string $name,
		public ParameterIn $in,
		public ?Markdown $description = null,
		public bool $required = false,
		public bool $deprecated = false,
	) {
		if ($in == ParameterIn::Path && !$required) {
			throw new InvalidValueProperties('Parameters in path must be required.');
		}
	}
}
