<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Framework\Identifier;
use Smolblog\Framework\Reader;

/**
 * An object that can retrieve an AuthRequestState from a repository.
 */
interface AuthRequestStateReader extends Reader {
	/**
	 * Get the indicated AuthRequestState from the repository. Should return null if not found.
	 *
	 * @param Identifier $id Generated ID from key.
	 * @return Entity Object identified by $id; null if it does not exist.
	 */
	public function get(Identifier $id): AuthRequestState;
}
