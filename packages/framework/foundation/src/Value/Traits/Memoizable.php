<?php

namespace Smolblog\Foundation\Value\Traits;

/**
 * A query that can be memoized.
 *
 * A query that does not need its full results calculated on every execution can be memoized. This stores the results
 * in memory for the remainder of the web request.
 *
 * Kind of a cache, but as there's no real way to invalidate it and it does not persist between requests, it's not
 * called that.
 *
 * @deprecated Use data interfaces instead of queries
 */
interface Memoizable {
	/**
	 * Get the memo key for this object.
	 *
	 * @return string
	 */
	public function getMemoKey(): string;
}
