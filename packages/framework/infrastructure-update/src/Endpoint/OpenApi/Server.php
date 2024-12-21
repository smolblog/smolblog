<?php

namespace Smolblog\Infrastructure\Endpoint\OpenApi;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Traits\ArrayType;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

readonly class Server extends Value implements SerializableValue {
	use SerializableValueKit;

	public function __construct(
		public Url $url,
		public ?Markdown $description = null,
		#[ArrayType(ServerVariable::class, isMap: true)] public ?array $variables = null,
	) {
		if (empty($variables)) {
			$this->variables = null;
		}
	}
}
