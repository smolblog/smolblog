<?php

namespace Smolblog\Core\Connector\Data;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Events\{ConnectionDeleted, ConnectionEstablished, ConnectionRefreshed};
use Smolblog\Core\Connector\Queries\{ConnectionBelongsToUser, ConnectionById, ConnectionsForUser};
use Smolblog\Foundation\Service\Messaging\Projection;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Save a Connection with its information.
 */
class ConnectionProjection implements Projection {
	public const TABLE = 'connections';

	/**
	 * Construct the service.
	 *
	 * @param ConnectionInterface $db Working DB connection.
	 */
	public function __construct(
		private ConnectionInterface $db,
	) {
	}

	/**
	 * Add or update a Connection.
	 *
	 * @param ConnectionEstablished $event Event to persist.
	 * @return void
	 */
	public function onConnectionEstablished(ConnectionEstablished $event) {
		$this->db->table(self::TABLE)->upsert(
			[
				'connection_uuid' => $event->connectionId->toString(),
				'user_uuid' => $event->userId->toString(),
				'provider' => $event->provider,
				'provider_key' => $event->providerKey,
				'display_name' => $event->displayName,
				'details' => json_encode($event->details),
			],
			'connection_uuid',
			['user_uuid', 'display_name', 'details']
		);
	}

	/**
	 * Update a Connection's info.
	 *
	 * @param ConnectionRefreshed $event Event to persist.
	 * @return void
	 */
	public function onConnectionRefreshed(ConnectionRefreshed $event) {
		$this->db->table(self::TABLE)->
			where('connection_uuid', $event->connectionId->toString())->
			update(['details' => json_encode($event->details)]);
	}

	/**
	 * Delete a Connection.
	 *
	 * @param ConnectionDeleted $event Event to persist.
	 * @return void
	 */
	public function onConnectionDeleted(ConnectionDeleted $event) {
		$this->db->table(self::TABLE)->
			where('connection_uuid', $event->connectionId->toString())->delete();
	}

	/**
	 * Retrieve a Connection by ID.
	 *
	 * @param ConnectionById $query Query to execute.
	 * @return void
	 */
	public function onConnectionById(ConnectionById $query) {
		$result = $this->db->table(self::TABLE)->where('connection_uuid', $query->connectionId->toString())->first();

		$query->setResults(self::connectionFromRow($result));
	}

	/**
	 * Retrieve all Connections attached to a given User.
	 *
	 * @param ConnectionsForUser $query Query to execute.
	 * @return void
	 */
	public function onConnectionsForUser(ConnectionsForUser $query) {
		$results = $this->db->table(self::TABLE)->where('user_uuid', $query->userId->toString())->get();

		$query->setResults($results->map(fn($cha) => self::connectionFromRow($cha))->all());
	}

	/**
	 * Determine if a Connection is attached to a User.
	 *
	 * @param ConnectionBelongsToUser $query Query to execute.
	 * @return void
	 */
	public function onConnectionBelongsToUser(ConnectionBelongsToUser $query) {
		$query->setResults($this->db->table(self::TABLE)->
			where('user_uuid', $query->userId->toString())->
			where('connection_uuid', $query->connectionId->toString())->exists());
	}

	/**
	 * Create a Connection object from a database row.
	 *
	 * @param object $data Results from the database.
	 * @return Connection
	 */
	public static function connectionFromRow(object $data): Connection {
		return new Connection(
			userId: Identifier::fromString($data->user_uuid),
			provider: $data->provider,
			providerKey: $data->provider_key,
			displayName: $data->display_name,
			details: json_decode($data->details, true),
		);
	}
}
