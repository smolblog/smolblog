<?php

namespace Smolblog\Mock\Projections;

use PDO;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Queries\ConnectionById;

class ConnectionProjection {
	public function __construct(private PDO $db) {}

	public function onConnectionById(ConnectionById $query) {
		$prepared = $this->db->prepare('SELECT
			user_id as userId,
			provider,
			provider_key as providerKey,
			displayName as displayName,
			details,
		FROM connections WHERE connection_id = ?');
		$prepared->execute([$query->connectionId]);
		$results = $prepared->fetch(mode: PDO::FETCH_ASSOC);
		$results['details'] = json_decode($results['details'], associative: true);
		return new Connection(...$results);
	}
}
