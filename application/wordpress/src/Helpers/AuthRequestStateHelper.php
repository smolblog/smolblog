<?php

/**
 * Attach the Smolblog AuthRequestState model to WordPress' transient funcitons.
 *
 * @package Smolblog\WP
 */

// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

namespace Smolblog\WP\Helpers;

use Smolblog\Core\Connection\Data\AuthRequestStateRepo;
use Smolblog\Core\Connection\Entities\AuthRequestState;

/**
 * Helper class to link WordPress and Smolblog transients.
 */
class AuthRequestStateHelper implements AuthRequestStateRepo {
	/**
	 * Get the indicated AuthRequestState from the repository. Should return null if not found.
	 *
	 * @param string $key Unique identifier for the object.
	 * @return AuthRequestState State identified by $id; null if it does not exist.
	 */
	public function getAuthRequestState(string $key): ?AuthRequestState {
		$state = get_transient($key);
		if (! is_array($state)) {
			return null;
		}

		return AuthRequestState::deserializeValue($state);
	}

	/**
	 * Save the given AuthRequestState
	 *
	 * @param AuthRequestState $state State to save.
	 * @return void
	 */
	public function saveAuthRequestState(AuthRequestState $state): void {
		set_transient($state->key, $state->serializeValue(), 60 * 15);
	}
}
