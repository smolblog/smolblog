<?php

namespace Smolblog\Framework\Messages;

/**
 * Object that can pass a Query to its handling service.
 *
 * A Query object passed to the `fetch` method should be routed to the appropriate service and passed to
 * its `handle$queryName` method.
 */
interface QueryBus {
	/**
	 * Execute the given Query by passing it to its service.
	 *
	 * @param Query $query Query to execute.
	 * @return mixed Result of the Query.
	 */
	public function fetch(Query $query): mixed;
}
