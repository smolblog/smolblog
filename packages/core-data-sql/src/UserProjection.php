<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\DomainEvent\EventListenerService;
use Cavatappi\Foundation\DomainEvent\ProjectionListener;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Infrastructure\Serialization\SerializationService;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Events\{ConnectionDeleted, ConnectionEstablished, ConnectionRefreshed};
use Smolblog\Core\User\User;
use Smolblog\Core\User\UserGrantedSudo;
use Smolblog\Core\User\UserRegistered;
use Smolblog\Core\User\UserRepo;

/**
 * Store and retrieve Connection objects.
 */
class UserProjection implements UserRepo, EventListenerService, DatabaseTableHandler {
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
		$table = $schema->createTable($tableName('users'));
		$table->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
		$table->addColumn('user_uuid', 'guid');
		$table->addColumn('key', 'string');
		$table->addColumn('user_obj', 'json');

		$table->addPrimaryKeyConstraint(
			PrimaryKeyConstraint::editor()->setUnquotedColumnNames('dbid')->create(),
		);
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
	 * Find out if any user exists with this ID.
	 *
	 * @param UuidInterface $userId ID to check.
	 * @return boolean
	 */
	public function hasUserWithId(UuidInterface $userId): bool {
		$query = $this->db->createQueryBuilder();
		$query
			->select('1')
			->from('user')
			->where('user_uuid = ?')
			->setParameter(0, $userId);
		$result = $query->fetchOne();

		return $result ? true : false;
	}

	/**
	 * Find out if any user exists with this key.
	 *
	 * @param string $key Key to check.
	 * @return boolean
	 */
	public function hasUserWithKey(string $key): bool {
		$query = $this->db->createQueryBuilder();
		$query
			->select('1')
			->from('user')
			->where('key = ?')
			->setParameter(0, $key);
		$result = $query->fetchOne();

		return $result ? true : false;
	}

	/**
	 * Fetch the given User from the repo; null if none is found.
	 *
	 * @param UuidInterface $userId User to fetch.
	 * @return User|null
	 */
	public function userById(UuidInterface $userId): ?User {
		$query = $this->db->createQueryBuilder();
		$query
			->select('user_obj')
			->from('users')
			->where('user_uuid = ?')
			->setParameter(0, $userId);
		$result = $query->fetchOne();

		if ($result === false) {
			return null;
		}

		// This has to do with different DB engines which we cannot currently test.
		return is_string($result)
			? $this->serde->fromJson($result, as: User::class)
			: $this->serde->fromArray($result, as: User::class); // @codeCoverageIgnore
	}

	#[ProjectionListener]
	public function onUserRegistered(UserRegistered $event): void {
		$user = new User(
			id: $event->entityId,
			key: $event->key,
			displayName: $event->displayName,
		);

		$this->db->insert('users', [
			'user_uuid' => $event->entityId,
			'key' => $event->key,
			'user_obj' => $this->serde->toJson($user),
		]);
	}

	public function onUserGrantedSudo(UserGrantedSudo $event): void {
		$existing = $this->userById($event->entityId);
		if (!isset($existing)) { return; }
		$updated = $existing->with(sudo: true);

		$this->db->update(
			'users',
			['user_obj' => $this->serde->toJson($updated)],
			['user_uuid' => $event->entityId],
		);
	}
}
