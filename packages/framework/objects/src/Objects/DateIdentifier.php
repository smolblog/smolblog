<?php

namespace Smolblog\Framework\Objects;

use DateTimeInterface;
use Ramsey\Uuid\Uuid;

/**
 * A date-based (version 7) UUID.
 */
class DateIdentifier extends Identifier {
	/**
	 * Create the UUID.
	 *
	 * @param DateTimeInterface|null $date Date to create UUID with. Uses current date by default.
	 */
	public function __construct(DateTimeInterface $date = null) {
		parent::__construct(internal: Uuid::uuid7($date));
	}
}
