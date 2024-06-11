<?php

namespace Smolblog\Foundation\Value\Traits;

use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * A "thing" that has a unique identifier and is often serialized to a database.
 */
interface Entity {
	/**
	 * Get the entity ID.
	 *
	 * Not all Entities have a hard-coded ID. Some may be derived from other data. Thus, a function.
	 *
	 * @return Identifier
	 */
	public function getId(): Identifier;
}
