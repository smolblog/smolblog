<?php

namespace Smolblog\Infrastructure\Endpoint\OpenApi;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

readonly class License extends Value implements SerializableValue {
	use SerializableValueKit;

	public function __construct(
		public string $name,
		public ?string $identifier = null,
		public ?Url $url = null,
	) {
		if (isset($identifier) && isset($url)) {
			throw new InvalidValueProperties('`identifier` and `url` are mutually exclusive.');
		}
		if (!isset($identifier) && !isset($url)) {
			throw new InvalidValueProperties('Either `identifier` or `url` are required.');
		}
	}
}
