<?php

namespace Smolblog\Core\Connector;

use Smolblog\Framework\Writer;

interface ConnectionWriter extends Writer {
	/**
	 * Save the given Connection to the repository.
	 *
	 * @param Connection $connection Connection to save.
	 * @return void
	 */
	public function save(Connection $connection): void;
}
