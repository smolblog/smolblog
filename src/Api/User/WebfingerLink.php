<?php

namespace Smolblog\Api\User;

use Smolblog\Framework\Objects\Value;

class WebfingerLink extends Value {
	public function __construct(
		public readonly string $rel,
		public readonly ?string $type = null,
		public readonly ?string $href = null,
		public readonly ?array $titles = null,
		public readonly ?array $properties = null,
	) {
	}
}
