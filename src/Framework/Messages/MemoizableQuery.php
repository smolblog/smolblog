<?php

namespace Smolblog\Framework\Messages;

/**
 * A query that can be memoized.
 *
 * A query that does not need its full results calculated on every execution can be memoized. This stores the results
 * in memory for the remainder of the web request.
 *
 * Kind of a cache, but as there's no real way to invalidate it and it does not persist between requests, it's not
 * called that.
 */
abstract class MemoizableQuery extends Query {
	/**
	 * Create a memo key for this object by hashing the class name and its JSON-encoded properties.
	 *
	 * @return string
	 */
	public function getMemoKey(): string {
		$values = $this->toArray();
		unset($values['results']);
		return static::class . ':' . md5(json_encode($values));
	}
}
