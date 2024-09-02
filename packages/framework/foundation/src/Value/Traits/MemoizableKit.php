<?php

namespace Smolblog\Foundation\Value\Traits;

/**
 * Provide a default Memoization key.
 *
 * @deprecated Use data interfaces instead of queries
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
