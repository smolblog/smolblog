<?php

namespace Smolblog\Foundation\Service\Query;

use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Framework\Messages\Query as DeprecatedQuery;

/**
 * A service that accepts a Query object, routes it to the correct handler, and returns the result.
 *
 * @deprecated Prefer data interfaces
 */
interface QueryBus {
	/**
	 * Execute the given Query and return the results.
	 *
	 * @param Query|DeprecatedQuery $query Query to execute.
	 * @return mixed Results of the Query.
	 */
	public function fetch(Query|DeprecatedQuery $query): mixed;
}
