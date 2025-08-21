<?php

namespace Smolblog\Foundation\v2\Value\Traits;

use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * A "thing" that has a unique identifier and is often serialized to a database.
 */
interface Entity {
	/**
	 * Unique identifier for this Entity.
	 *
	 * @var Identifier
	 */
	public Identifier $id { get; }
}
