<?php

namespace Smolblog\Framework;

use Stringable;

/**
 * Represents an object that can be uniquely identified.
 */
abstract class Entity extends Value implements Stringable {
	/**
	 * Create the Entity. This constructor exists mostly for use by subclasses.
	 *
	 * @param Identifier $id Unique identification for this object.
	 */
	public function __construct(public readonly Identifier $id) {
	}

	/**
	 * Returns the fully-qualified class name and the object's $id. Used in comparisons.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return static::class . ':' . strval($this->id);
	}
}
