<?php

namespace Smolblog\Framework\Messages;

/**
 * Indicates that an object provides a Query to check whether it can be executed.
 *
 * If no authorization is required, the object should use the NoAuthNeededKit trait.
 */
interface Authorizable {
	/**
	 * Provide a Query object that will provide a truthy value if this object can be run; null if no authorization is
	 * required.
	 *
	 * @return Query|null
	 */
	public function getAuthorizationQuery(): ?Query;
}
