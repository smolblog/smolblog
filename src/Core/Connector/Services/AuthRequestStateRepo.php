<?php

namespace Smolblog\Core\Connector\Entities;

interface AuthRequestStateRepo {
	/**
	 * Save the given AuthRequestState
	 *
	 * @param AuthRequestState $state State to save.
	 * @return void
	 */
	public function save(AuthRequestState $state): void;

	/**
	 * Get the given AuthRequestState
	 *
	 * @param string $key Key of the state to retrieve.
	 * @return AuthRequestState
	 */
	public function get(string $key): AuthRequestState;
}
