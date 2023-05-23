<?php

namespace Smolblog\Api\User;

use Smolblog\Framework\Objects\Value;

class WebfingerResponse extends Value {
	public function __construct(
		public readonly string $subject,
		public readonly ?array $aliases = null,
		public readonly ?array $properties = null,
		public readonly ?array $links = null,
	) {
	}
}
