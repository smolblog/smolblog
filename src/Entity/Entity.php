<?php

namespace Smolblog\Core\Entity;

/**
 * Represents an object that can be uniquely identified.
 */
abstract class Entity {
	/**
	 * Create the Entity. This constructor exists mostly for use by subclasses.
	 *
	 * @param integer|string $id Unique identification for this object.
	 */
	public function __construct(public readonly int|string $id) {
	}
}
