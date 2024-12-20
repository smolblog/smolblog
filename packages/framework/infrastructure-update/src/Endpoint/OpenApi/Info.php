<?php

namespace Smolblog\Infrastructure\Endpoint\OpenApi;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 *
 */
readonly class Info extends Value implements SerializableValue {
	use SerializableValueKit;

	public function __construct(
		public string $title,
		public string $version,
		public ?string $description,
		public ?Url $termsOfService,
		public ?object $contact,
		public ?object $license,
	) {
	}
}
