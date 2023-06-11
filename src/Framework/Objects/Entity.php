<?php

namespace Smolblog\Framework\Objects;

use Stringable;

/**
 * Represents an object that can be uniquely identified.
 */
abstract class Entity extends Value implements Stringable {
	use EntityKit;

	/**
	 * Unique identifier (UUID) for this particular entity.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $id;

	/**
	 * Create the Entity. This constructor exists mostly for use by subclasses.
	 *
	 * @param Identifier $id Unique identification for this object.
	 */
	public function __construct(Identifier $id) {
		$this->id = $id;
	}
}
