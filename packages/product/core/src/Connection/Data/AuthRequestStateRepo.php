<?php

namespace Smolblog\Core\Connection\Data;

use Smolblog\Core\Connection\Entities\AuthRequestState;

interface AuthRequestStateRepo {
	/**
	 * Save the given AuthRequestState
	 *
	 * @param AuthRequestState $state State to save.
	 * @return void
	 */
	public function saveAuthRequestState(AuthRequestState $state): void;

	/**
	 * Get the given AuthRequestState
	 *
	 * @param string $key Key of the state to retrieve.
	 * @return AuthRequestState|null
	 */
	public function getAuthRequestState(string $key): ?AuthRequestState;
}
