<?php

namespace Smolblog\Framework\Objects;

use Ramsey\Uuid\Uuid;

class RandomIdentifier extends Identifier {
	public function __construct() {
		parent::__construct(internal: Uuid::uuid4());
	}
}
