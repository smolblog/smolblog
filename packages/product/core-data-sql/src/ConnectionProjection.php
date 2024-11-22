<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Connection as DatabaseConnection;
use Doctrine\DBAL\Schema\Schema;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Events\{ConnectionDeleted, ConnectionEstablished, ConnectionRefreshed};
use Smolblog\Foundation\Service\Event\EventListenerService;
use Smolblog\Foundation\Service\Event\ProjectionListener;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Store and retrieve Connection objects.
 */
class ConnectionProjection implements ConnectionRepo, EventListenerService, DatabaseTableHandler {
	/**
	 * Create the channel tables.
	 *
	 * Currently creates both a table for storing Channel state and a linking table for Channels and Sites. As Sites
	 * grows more robust, this may be broken out into its own class.
	 *
	 * @param Schema $schema Schema to add the channel tables to.
	 * @return Schema
	 */
	public static function addTableToSchema(Schema $schema): Schema {
		$table = $schema->createTable('connections');
		$table->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
		$table->addColumn('connection_uuid', 'guid');
		$table->addColumn('user_uuid', 'guid');
		$table->addColumn('connection_obj', 'json');

		$table->setPrimaryKey(['dbid']);
		$table->addUniqueIndex(['connection_uuid']);
		$table->addIndex(['user_uuid']);

		return $schema;
	}

	/**
	 * Create the service.
	 *
	 * @param DatabaseConnection $db Working database connection.
	 */
	public function __construct(private DatabaseConnection $db) {
	}

	/**
	 * Find out if the given Connection belongs to the given User.
	 *
	 * @param Identifier $connectionId Connection to check.
	 * @param Identifier $userId       User to check.
	 * @return boolean True if the given User created the given Connection.
	 */
	public function connectionBelongsToUser(Identifier $connectionId, Identifier $userId): bool {
		$query = $this->db->createQueryBuilder();
		$query->select('1')
			->from('connections')
			->where('connection_uuid = ?')
			->andWhere('user_uuid = ?')
			->setParameter(0, $connectionId)
			->setParameter(1, $userId);
		$result = $query->fetchOne();

		return $result ? true : false;
	}

	/**
	 * Fetch the given Connection from the repo; null if none is found.
	 *
	 * @param Identifier $connectionId Connection to fetch.
	 * @return Connection|null
	 */
	public function connectionById(Identifier $connectionId): ?Connection {
		$query = $this->db->createQueryBuilder();
		$query
			->select('connection_obj')
			->from('connections')
			->where('connection_uuid = ?')
			->setParameter(0, $connectionId);
		$result = $query->fetchOne();

		if ($result === false) {
			return null;
		}

		// This has to do with different DB engines which we cannot currently test.
		return is_string($result) ?
			Connection::fromJson($result) :
			Connection::deserializeValue($result); // @codeCoverageIgnore
	}

	/**
	 * Get all Connections for a given User.
	 *
	 * @param Identifier $userId User whose Connections are being fetched.
	 * @return Connection[]
	 */
	public function connectionsForUser(Identifier $userId): array {
		$query = $this->db->createQueryBuilder();
		$query->select('connection_obj')->from('connections')->where('user_uuid = ?')->setParameter(0, $userId);
		$results = $query->fetchFirstColumn();

		return array_map(
			fn($res) => is_string($res) ? Connection::fromJson($res) : Connection::deserializeValue($res),
			$results,
		);
	}

	/**
	 * Save connection information.
	 *
	 * @param ConnectionEstablished $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener]
	public function onConnectionEstablished(ConnectionEstablished $event): void {
		$checkQuery = $this->db->createQueryBuilder();
		$checkQuery
			->select('dbid')
			->from('connections')
			->where('connection_uuid = ?')
			->setParameter(0, $event->entityId);
		$dbid = $checkQuery->fetchOne();

		$data = [
			'connection_uuid' => $event->entityId,
			'user_uuid' => $event->userId,
			'connection_obj' => json_encode($event->getConnectionObject()),
		];

		if ($dbid) {
			$this->db->update('connections', $data, ['dbid' => $dbid]);
		} else {
			$this->db->insert('connections', $data);
		}
	}

	/**
	 * Save updated connection information.
	 *
	 * @param ConnectionRefreshed $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener]
	public function onConnectionRefreshed(ConnectionRefreshed $event): void {
		$current = $this->connectionById($event->entityId ?? Identifier::nil());
		if (!isset($current)) {
			return;
		}

		$updated = $current->with(details: $event->details);
		$this->db->update(
			'connections',
			['connection_obj' => json_encode($updated)],
			['connection_uuid' => $event->entityId]
		);
	}

	/**
	 * Remove a connection from the system.
	 *
	 * @param ConnectionDeleted $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener]
	public function onConnectionDeleted(ConnectionDeleted $event): void {
		$this->db->delete('connections', ['connection_uuid' => $event->entityId]);
	}
}
