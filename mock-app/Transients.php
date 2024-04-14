<?php

namespace Smolblog\Mock;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Database\ConnectionInterface;
use PDO;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Core\Connector\Services\AuthRequestStateRepo;
use Smolblog\Foundation\Value\Fields\Identifier;

class Transients implements AuthRequestStateRepo {
	public function __construct(private ConnectionInterface $db) {}

	/**
	 * Save the given AuthRequestState
	 *
	 * @param AuthRequestState $state State to save.
	 * @return void
	 */
	public function saveAuthRequestState(AuthRequestState $state): void {
		$this->db->table('temp_options')->insert([
			'key' => 'authRequest-' . $state->key,
			'value' => json_encode($state),
			'expires' => (new DateTimeImmutable())->add(new DateInterval('P15M'))->format(DateTimeInterface::RFC3339_EXTENDED),
		]);
	}

	/**
	 * Get the given AuthRequestState
	 *
	 * @param string $key Key of the state to retrieve.
	 * @return AuthRequestState
	 */
	public function getAuthRequestState(string $key): ?AuthRequestState {
		$json = $this->db->table('temp_options')->where('key', '=', 'authRequest-' . $key)->value('value');
		if (!$json) { return null; }
		return AuthRequestState::jsonDeserialize($json);
	}
}
