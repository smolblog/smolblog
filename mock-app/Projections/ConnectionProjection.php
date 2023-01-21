<?php

namespace Smolblog\Mock\Projections;

use PDO;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Events\ConnectionEstablished;
use Smolblog\Core\Connector\Queries\ConnectionBelongsToUser;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Framework\Objects\Identifier;

class ConnectionProjection {
	public function __construct(private PDO $db) {}

	public function onConnectionEstablished(ConnectionEstablished $event) {
		$prepared = $this->db->prepare('INSERT INTO connections
			(connection_id, user_id, provider, provider_key, display_name, details)
			VALUES (:id, :userId, :provider, :providerKey, :displayName, :details)');
		$prepared->execute([
			'id' => $event->connectionId->toByteString(),
			'userId' => $event->userId->toByteString(),
			'provider' => $event->provider,
			'providerKey' => $event->providerKey,
			'displayName' => $event->displayName,
			'details' => json_encode($event->details),
		]);
	}

	public function onConnectionById(ConnectionById $query) {
		$prepared = $this->db->prepare('SELECT
			user_id as userId,
			provider,
			provider_key as providerKey,
			display_name as displayName,
			details
		FROM connections WHERE connection_id = ?');
		$prepared->execute([$query->connectionId->toByteString()]);
		$results = $prepared->fetch(mode: PDO::FETCH_ASSOC);
		if (empty($results)) {
			$query->results = null;
			return;
		}

		$query->results = new Connection(
			userId: Identifier::fromByteString($results['userId']),
			provider: $results['provider'],
			providerKey: $results['providerKey'],
			displayName: $results['displayName'],
			details: json_decode($results['details'], associative: true),
		);
	}

	public function onConnectionBelongsToUser(ConnectionBelongsToUser $query) {
		$prepared = $this->db->prepare('SELECT 1 FROM connections WHERE connection_id = ? AND user_id = ?');
		$prepared->execute([$query->connectionId->toByteString(), $query->userId->toByteString()]);
		$query->results = !empty($prepared->fetch());
	}
}
