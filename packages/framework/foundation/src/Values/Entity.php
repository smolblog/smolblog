<?php

namespace Smolblog\Framework\Foundation\Values;

use Smolblog\Framework\Foundation\Value;

/**
 * A "thing" that has a unique identifier and is often serialized to a database.
 */
readonly class Entity extends Value {
	/**
	 * Construct the entity
	 *
	 * @param Identifier $id ID of the entity.
	 */
	public function __construct(public Identifier $id) {
	}
}
