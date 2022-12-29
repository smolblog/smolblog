<?php

namespace Smolblog\Framework\Messages;

/**
 * Indicates that a query can be memoized.
 *
 * A query that does not need its full results calculated on every execution can be memoized. This stores the results
 * in memory for the remainder of the web request.
 *
 * Kind of a cache, but as there's no real way to invalidate it and it does not persist between requests, it's not
 * called that.
 */
interface MemoizableQuery extends StoppableMessage {
	/**
	 * Get a key that uniquely identifies the Query and its parameters.
	 *
	 * @return string
	 */
	public function getMemoKey(): string;
}
