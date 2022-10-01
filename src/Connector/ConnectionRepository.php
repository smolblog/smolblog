<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Entity\Repository;

interface ConnectionRepository extends Repository {
	/**
	 * Save the given Connection to the repository.
	 *
	 * @param Connection $connection Connection to save.
	 * @return void
	 */
	public function save(Connection $connection): void;
}
