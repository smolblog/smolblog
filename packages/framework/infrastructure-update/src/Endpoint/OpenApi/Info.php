<?php

namespace Smolblog\Infrastructure\Endpoint\OpenApi;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Markdown;
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
		public ?string $summary = null,
		public ?Markdown $description = null,
		public ?Url $termsOfService = null,
		public ?Contact $contact = null,
		public ?License $license = null,
	) {
	}
}
