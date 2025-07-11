<?php

namespace Smolblog\Foundation\Value\Traits;

use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * A "thing" that has a unique identifier and is often serialized to a database.
 */
interface Entity {
	/**
	 * All entities have a unique ID.
	 *
	 * @var Identifier
	 */
	public Identifier $id { get; }

	/**
	 * Get the entity ID.
	 *
	 * Not all Entities have a hard-coded ID. Some may be derived from other data. Thus, a function.
	 * @deprecated use the $id property instead.
	 *
	 * @return Identifier
	 */
	public function getId(): Identifier;
}
