<?php

namespace Smolblog\Core\Post;

use Smolblog\Framework\Entity;
use Smolblog\Framework\Identifier;

/**
 * A unit of content for a Post. Just an empty Entity class because the only
 * common piece is an ID. Every Block's data could look different, and parsing
 * that data into another format is another class' job.
 */
abstract class Block extends Entity {
	/**
	 * Create with the given ID. Creates a date-based ID if not provided.
	 *
	 * @param Identifier|null $id ID if one exists.
	 */
	public function __construct(Identifier $id = null) {
		parent::__construct(id: $id ?? Identifier::createFromDate());
	}
}
