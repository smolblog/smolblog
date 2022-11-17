<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Entity\Reader;

/**
 * An object that can retrieve an AuthRequestState from a repository.
 */
interface AuthRequestStateReader extends Reader {
	/**
	 * Get the indicated AuthRequestState from the repository. Should return null if not found.
	 *
	 * @param string|integer $id Unique identifier for the object.
	 * @return Entity Object identified by $id; null if it does not exist.
	 */
	public function get(string|int $id): AuthRequestState;
}
