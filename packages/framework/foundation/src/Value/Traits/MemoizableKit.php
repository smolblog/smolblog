<?php

namespace Smolblog\Foundation\Value\Traits;

/**
 * Provide a default Memoization key.
 */
trait MemoizableKit {
	/**
	 * Create a memo key for this object by hashing the class name and its JSON-encoded properties.
	 *
	 * @return string
	 */
	public function getMemoKey(): string {
		return static::class . ':' . md5(json_encode($this));
	}
}
