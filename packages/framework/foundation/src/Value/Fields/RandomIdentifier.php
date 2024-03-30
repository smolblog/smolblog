<?php

namespace Smolblog\Foundation\Value\Fields;

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
