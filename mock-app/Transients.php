<?php

namespace Smolblog\Mock;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use PDO;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Core\Connector\Services\AuthRequestStateRepo;
use Smolblog\Framework\Objects\Identifier;

class Transients implements AuthRequestStateRepo {
	public function __construct(private PDO $db) {}

	/**
	 * Save the given AuthRequestState
	 *
	 * @param AuthRequestState $state State to save.
	 * @return void
	 */
	public function saveAuthRequestState(AuthRequestState $state): void {
		$query = $this->db->prepare('INSERT INTO temp_options ("key", "value", "expires") VALUES (?, ?, ?)');
		$query->execute([
			'authRequest-' . $state->key,
			json_encode($state),
			(new DateTimeImmutable())->add(new DateInterval('P15M'))->format(DateTimeInterface::RFC3339_EXTENDED),
		]);
	}

	/**
	 * Get the given AuthRequestState
	 *
	 * @param string $key Key of the state to retrieve.
	 * @return AuthRequestState
	 */
	public function getAuthRequestState(string $key): ?AuthRequestState {
		$query = $this->db->prepare('SELECT "value" FROM temp_options WHERE "key" = ?');
		$query->execute(['authRequest-' . $key]);
		$json = $query->fetchColumn();
		if (false === $json) { return null; }
		return AuthRequestState::jsonDeserialize($json);
	}
}
