<?php

namespace Smolblog\Core\Entity;

/**
 * Represents an object that can be uniquely identified.
 */
abstract class Entity {
	/**
	 * Unique identification for this object.
	 *
	 * @var integer|string
	 */
	public readonly int|string $id; //phpcs:ignore
}
