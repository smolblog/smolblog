<?php

namespace Smolblog\Framework;

/**
 * Indicates that an object provides a Query to check whether it can be executed.
 */
interface Authorizable {
	/**
	 * Provide a Query object that will provide a truthy value if this object can be run, or null if no authorization
	 * is required.
	 *
	 * @return Query|null
	 */
	public function getAuthorizationQuery(): ?Query;
}
