<?php

namespace Smolblog\Framework\Foundation\Value\Traits;

use Smolblog\Framework\Foundation\Value\Fields\Identifier;

/**
 * Default implementation of an Entity.
 *
 * Consuming class MUST set $this->id in the constructor.
 */
trait EntityKit {
	/**
	 * The Entity's unique identifier.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $id;

	/**
	 * Get the Entity's ID.
	 *
	 * @return Identifier
	 */
	public function getId(): Identifier {
		return $this->id;
	}
}
