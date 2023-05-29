<?php

namespace Smolblog\Api\ActivityPub;

use Smolblog\Framework\Objects\Value;

abstract class ActivityPubObject extends Value {
	public function __construct(
		public readonly string $id,
		public readonly string $type,
	) {
	}

	public function toArray(): array {
		return array_filter(parent::toArray(), fn($item) => isset($item));
	}
}
