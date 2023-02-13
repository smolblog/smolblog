<?php

namespace Smolblog\RestApiBase;

use Smolblog\Framework\Objects\Value;

class Response extends Value {
	public function __construct(
		public readonly Value $body,
		public readonly int $status = 200,
	) {
	}
}
