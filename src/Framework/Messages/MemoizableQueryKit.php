<?php

namespace Smolblog\Framework\Objects;

use Smolblog\Framework\Messages\StoppableMessageKit;

/**
 * Easy implementation for a MemoizableQuery. It's recommended to pair this with SerializableKit if the object is
 * unusually complex.
 */
trait MemoizableQueryKit {
	use StoppableMessageKit;

	/**
	 * Create a memo key for this object by hashing the class name and its JSON-encoded properties.
	 *
	 * @return string
	 */
	public function getMemoKey(): string {
		return md5(static::class . json_encode($this));
	}
}
