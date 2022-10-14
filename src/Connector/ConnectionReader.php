<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Entity\Reader;

/**
 * An object that can retrieve an Connection from a repository.
 */
interface ConnectionReader extends Reader {
	/**
	 * Get the indicated Connection from the repository. Should return null if not found.
	 *
	 * @param string|integer $id Unique identifier for the object.
	 * @return Connection Object identified by $id; null if it does not exist.
	 */
	public function get(string|int $id): Connection;
}
