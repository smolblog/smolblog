<?php

namespace Smolblog\Framework\Objects;

use DateTimeInterface;
use Ramsey\Uuid\Uuid;

class DateIdentifier extends Identifier {
	public function __construct(DateTimeInterface $date = null) {
		parent::__construct(internal: Uuid::uuid7($date));
	}
}
