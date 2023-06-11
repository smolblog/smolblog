<?php

namespace Smolblog\Framework\Messages;

/**
 * An object that asks the domain model a thing.
 *
 * A query can be as simple as fetching an object from a repo or represent something slightly more complex. By
 * creating objects and sending them through a central orchestrator, we can more easily cache queries or send
 * complex queries to specialized handlers.
 *
 * All Queries that can be memoized should extend MemoizableQuery.
 */
abstract class Query extends Message {
	/**
	 * Stores the results of the query. Initialized to `null`.
	 *
	 * @var mixed
	 */
	public mixed $results = null;
}
