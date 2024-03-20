<?php

namespace Smolblog\Framework\Foundation\Values;

use Ramsey\Uuid\Uuid;

/**
 * A randomly-generated (version 4) UUID.
 */
readonly class RandomIdentifier extends Identifier {
	/**
	 * Create the UUID.
	 */
	public function __construct() {
		parent::__construct(internal: Uuid::uuid4());
	}
}
