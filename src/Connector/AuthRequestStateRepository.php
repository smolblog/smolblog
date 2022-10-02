<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Entity\Repository;

interface AuthRequestStateRepository extends Repository {
	/**
	 * Save the given AuthRequestState
	 *
	 * @param AuthRequestState $state State to save.
	 * @return void
	 */
	public function save(AuthRequestState $state): void;
}
