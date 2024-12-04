<?php

namespace Smolblog\Foundation\Value\Fields;

use DateTimeInterface;
use Ramsey\Uuid\Uuid;

/**
 * A date-based (version 7) UUID.
 */
readonly class DateIdentifier extends Identifier {
	/**
	 * Create the UUID.
	 *
	 * @param DateTimeInterface|null $date Date to create UUID with. Uses current date by default.
	 */
	public function __construct(DateTimeInterface|DateTimeField|null $date = null) {
		$dateObj = $date instanceof DateTimeField ? $date->object : $date;
		parent::__construct(internal: Uuid::uuid7($dateObj));
	}
}
