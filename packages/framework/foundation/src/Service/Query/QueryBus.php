<?php

namespace Smolblog\Foundation\Service\Query;

use Smolblog\Foundation\Value\Messages\Query;

/**
 * A service that accepts a Query object, routes it to the correct handler, and returns the result.
 */
interface QueryBus {
	/**
	 * Execute the given Query and return the results.
	 *
	 * @param Query $query Query to execute.
	 * @return mixed Results of the Query.
	 */
	public function fetch(Query $query): mixed;
}
