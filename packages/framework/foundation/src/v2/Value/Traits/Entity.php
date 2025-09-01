<?php

namespace Smolblog\Foundation\v2\Value\Traits;

use Ramsey\Uuid\UuidInterface;

/**
 * A "thing" that has a unique identifier and is often serialized to a database.
 */
interface Entity {
	/**
	 * Unique identifier for this Entity.
	 *
	 * @var UuidInterface
	 */
	public UuidInterface $id { get; }
}
