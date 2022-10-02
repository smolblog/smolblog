<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Entity\Writer;

interface AuthRequestStateWriter extends Writer {
	/**
	 * Save the given AuthRequestState
	 *
	 * @param AuthRequestState $state State to save.
	 * @return void
	 */
	public function save(AuthRequestState $state): void;
}
