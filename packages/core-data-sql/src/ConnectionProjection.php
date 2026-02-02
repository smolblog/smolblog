<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\DomainEvent\EventListenerService;
use Cavatappi\Foundation\DomainEvent\ProjectionListener;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Infrastructure\Serialization\SerializationService;
use Doctrine\DBAL\Schema\Schema;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Events\{ConnectionDeleted, ConnectionEstablished, ConnectionRefreshed};

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
	 * @param Schema   $schema    Schema to add the channel tables to.
	 * @param callable $tableName Function to create a prefixed table name from a given table name.
	 * @return Schema
	 */
	public static function addTableToSchema(Schema $schema, callable $tableName): Schema {
		$table = $schema->createTable($tableName('connections'));
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
	 * @param DatabaseService      $db    Working database connection.
	 * @param SerializationService $serde Configured (de)serialization service.
	 */
	public function __construct(
		private DatabaseService $db,
		private SerializationService $serde,
	) {}

	/**
	 * Find out if the given Connection belongs to the given User.
	 *
	 * @param UuidInterface $connectionId Connection to check.
	 * @param UuidInterface $userId       User to check.
	 * @return boolean True if the given User created the given Connection.
	 */
	public function connectionBelongsToUser(UuidInterface $connectionId, UuidInterface $userId): bool {
		$query = $this->db->createUnprefixedQueryBuilder();
		$query->select('1')
			->from($this->db->tableName('connections'))
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
	 * @param UuidInterface $connectionId Connection to fetch.
	 * @return Connection|null
	 */
	public function connectionById(UuidInterface $connectionId): ?Connection {
		$query = $this->db->createUnprefixedQueryBuilder();
		$query
			->select('connection_obj')
			->from($this->db->tableName('connections'))
			->where('connection_uuid = ?')
			->setParameter(0, $connectionId);
		$result = $query->fetchOne();

		if ($result === false) {
			return null;
		}

		// This has to do with different DB engines which we cannot currently test.
		return is_string($result)
			? $this->serde->fromJson($result, as: Connection::class)
			: $this->serde->fromArray($result, as: Connection::class); // @codeCoverageIgnore
	}

	/**
	 * Get all Connections for a given User.
	 *
	 * @param UuidInterface $userId User whose Connections are being fetched.
	 * @return Connection[]
	 */
	public function connectionsForUser(UuidInterface $userId): array {
		$query = $this->db->createUnprefixedQueryBuilder();
		$query
			->select('connection_obj')
			->from($this->db->tableName('connections'))
			->where('user_uuid = ?')
			->setParameter(0, $userId);
		$results = $query->fetchFirstColumn();

		return array_map(
			fn($res) => is_string($res) ? $this->serde->fromJson($res, as: Connection::class) : $this->serde->fromArray($res, as: Connection::class),
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
		$checkQuery = $this->db->createUnprefixedQueryBuilder();
		$checkQuery
			->select('dbid')
			->from($this->db->tableName('connections'))
			->where('connection_uuid = ?')
			->setParameter(0, $event->entityId);
		$dbid = $checkQuery->fetchOne();

		$data = [
			'connection_uuid' => $event->entityId,
			'user_uuid' => $event->userId,
			'connection_obj' => $this->serde->toJson($event->getConnectionObject()),
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
		$current = $this->connectionById($event->entityId ?? UuidFactory::nil());
		if (!isset($current)) {
			return;
		}

		$updated = $current->with(details: $event->details);
		$this->db->update(
			'connections',
			['connection_obj' => $this->serde->toJson($updated)],
			['connection_uuid' => $event->entityId],
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
