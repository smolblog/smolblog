<?php

namespace Smolblog\Infrastructure\Endpoint\OpenApi;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Email;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

readonly class Contact extends Value implements SerializableValue {
	use SerializableValueKit;

	public function __construct(
		public ?string $name = null,
		public ?Url $url = null,
		public ?Email $email = null,
	) {
	}
}
