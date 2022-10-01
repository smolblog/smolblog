<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Entity\Repository;

interface ConnectionCreationStateRepository extends Repository {
	/**
	 * Save the given ConnectionCreationState
	 *
	 * @param ConnectionCreationState $state State to save.
	 * @return void
	 */
	public function save(ConnectionCreationState $state): void;
}
