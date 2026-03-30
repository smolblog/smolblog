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
use Smolblog\Core\Site\Data\SiteRepo;
use Smolblog\Core\Site\Data\SiteUserRepo;
use Smolblog\Core\Site\Entities\Site;
use Smolblog\Core\Site\Entities\SitePermissionLevel;
use Smolblog\Core\Site\Events\SiteCreated;
use Smolblog\Core\Site\Events\SiteDetailsUpdated;
use Smolblog\Core\Site\Events\UserSitePermissionsSet;
use Smolblog\Core\User\User;
use Smolblog\Core\User\UserGrantedSudo;
use Smolblog\Core\User\UserRegistered;
use Smolblog\Core\User\UserRepo;

/**
 * Store and retrieve Site objects.
 */
class SiteProjection implements SiteRepo, SiteUserRepo, EventListenerService, DatabaseTableHandler {
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
		$siteTable = $schema->createTable($tableName('sites'));
		$siteTable->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
		$siteTable->addColumn('site_uuid', 'guid');
		$siteTable->addColumn('key', 'string');
		$siteTable->addColumn('user_uuid', 'guid');
		$siteTable->addColumn('site_obj', 'json');

		$siteTable->addPrimaryKeyConstraint(
			PrimaryKeyConstraint::editor()->setUnquotedColumnNames('dbid')->create(),
		);
		$siteTable->addUniqueIndex(['site_uuid']);
		$siteTable->addUniqueIndex(['key']);
		$siteTable->addIndex(['user_uuid']);

		$siteUserTable = $schema->createTable($tableName('sites_users'));
		$siteUserTable->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
		$siteUserTable->addColumn('site_uuid', 'guid');
		$siteUserTable->addColumn('user_uuid', 'guid');
		$siteUserTable->addColumn('level', 'string');

		$siteUserTable->addPrimaryKeyConstraint(
			PrimaryKeyConstraint::editor()->setUnquotedColumnNames('dbid')->create(),
		);
		$siteUserTable->addUniqueIndex(['user_uuid', 'site_uuid']);

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
	 * Return true if a site with the given ID exists.
	 *
	 * @param UuidInterface $siteId ID to check.
	 * @return boolean
	 */
	public function hasSiteWithId(UuidInterface $siteId): bool {
		$query = $this->db->createQueryBuilder();
		$query
			->select('1')
			->from('sites')
			->where('site_uuid = :site')
			->setParameter('site', $siteId);
		$result = $query->fetchOne();

		return $result ? true : false;
	}

	/**
	 * Return true if a site with the given key exists.
	 *
	 * @param string $key Key to check.
	 * @return boolean
	 */
	public function hasSiteWithKey(string $key): bool {
		$query = $this->db->createQueryBuilder();
		$query
			->select('1')
			->from('sites')
			->where('key = ?')
			->setParameter(0, $key);
		$result = $query->fetchOne();

		return $result ? true : false;
	}

	/**
	 * Get the site object for the given ID.
	 *
	 * @param UuidInterface $siteId Site to retrieve.
	 * @return Site|null
	 */
	public function siteById(UuidInterface $siteId): ?Site {
		$query = $this->db->createQueryBuilder();
		$query
			->select('site_obj')
			->from('sites')
			->where('site_uuid = :site')
			->setParameter('site', $siteId);
		$result = $query->fetchOne();

		if ($result === false) {
			return null;
		}

		// This has to do with different DB engines which we cannot currently test.
		return is_string($result)
			? $this->serde->fromJson($result, as: Site::class)
			: $this->serde->fromArray($result, as: Site::class); // @codeCoverageIgnore
	}

	/**
	 * Get the sites belonging to a given user.
	 *
	 * @param UuidInterface $userId User whose sites to retrieve.
	 * @return Site[]
	 */
	public function sitesForUser(UuidInterface $userId): array {
		$query = $this->db->createQueryBuilder();
		$query
			->select('site_obj')
			->from('sites')
			->where('user_uuid = :user')
			->setParameter('user', $userId)
			->orderBy('dbid', 'DESC');
		$results = $query->fetchFirstColumn();

		return array_map(
			fn($ser) => is_string($ser)
				? $this->serde->fromJson($ser, Site::class)
				: $this->serde->fromArray($ser, Site::class), // @codeCoverageIgnore
			$results,
		);
	}

	/**
	 * Return true if a user with the given ID is permissioned for the given site.
	 *
	 * If there is an entry but the user's permissions are 'None', this should return false.
	 *
	 * @param UuidInterface $userId ID to check.
	 * @param UuidInterface $siteId ID to check.
	 * @return boolean
	 */
	public function hasUserForSite(UuidInterface $userId, UuidInterface $siteId): bool {
		$query = $this->db->createQueryBuilder();
		$query
			->select('1')
			->from('sites_users')
			->where('site_uuid = :site')
			->where('user_uuid = :user')
			->setParameter('site', $siteId)
			->setParameter('user', $userId);
		$result = $query->fetchOne();

		return $result ? true : false;
	}

	/**
	 * Get the permssions for a given user for the given site.
	 *
	 * If no permissions have been set, the implementing class should return ::None.
	 *
	 * @param UuidInterface $userId ID to check.
	 * @param UuidInterface $siteId ID to check.
	 * @return SitePermissionLevel
	 */
	public function permissionsForUser(UuidInterface $userId, UuidInterface $siteId): SitePermissionLevel {
		$query = $this->db->createQueryBuilder();
		$query
			->select('level')
			->from('sites_users')
			->where('site_uuid = :site')
			->where('user_uuid = :user')
			->setParameter('site', $siteId)
			->setParameter('user', $userId);

		$result = $query->fetchOne();
		if ($result === false) {
			return SitePermissionLevel::None;
		}

		return SitePermissionLevel::tryFrom($result) ?? SitePermissionLevel::None;
	}

	/**
	 * Get the IDs for users that have permissions for the given site.
	 *
	 * @param UuidInterface $siteId Site whose users to retrieve.
	 * @return UuidInterface[]
	 */
	public function userIdsForSite(UuidInterface $siteId): array {
		$query = $this->db->createQueryBuilder();
		$query
			->select('user_uuid')
			->from('sites_users')
			->where('site_uuid = :site')
			->setParameter('site', $siteId)
			->orderBy('dbid', 'DESC');
		$results = $query->fetchFirstColumn();

		return array_map(
			fn($ser) => UuidFactory::fromString($ser),
			$results,
		);
	}

	#[ProjectionListener]
	public function onSiteCreated(SiteCreated $event): void {
		$site = $event->getSiteObject();

		$this->db->insert('sites', [
			'site_uuid' => $site->id,
			'key' => $site->key,
			'user_uuid' => $site->userId,
			'site_obj' => $this->serde->toJson($site),
		]);
		$this->db->insert('sites_users', [
			'site_uuid' => $site->id,
			'user_uuid' => $site->userId,
			'level' => SitePermissionLevel::Admin->value,
		]);
	}

	public function onSiteDetailsUpdated(SiteDetailsUpdated $event): void {
		$existing = $this->siteById($event->aggregateId);
		if (!isset($existing)) {
			return;
		}

		$updated = $existing;
		if (isset($event->displayName)) {
			$updated = $updated->with(displayName: $event->displayName);
		}
		if (isset($event->description)) {
			$updated = $updated->with(description: $event->description);
		}
		if (isset($event->pictureId)) {
			$updated = $updated->with(pictureId: $event->pictureId);
		}

		$this->db->update(
			'sites',
			['site_obj' => $this->serde->toJson($updated)],
			['site_uuid' => $event->aggregateId],
		);
	}

	public function onUserSitePermissionsSet(UserSitePermissionsSet $event): void {
		$checkQuery = $this->db->createQueryBuilder();
		$checkQuery
			->select('dbid')
			->from('sites_users')
			->where('user_uuid = :user')
			->where('site_uuid = :site')
			->setParameter('user', $event->entityId)
			->setParameter('site', $event->aggregateId);
		$dbid = $checkQuery->fetchOne();

		$data = [
			'user_uuid' => $event->entityId,
			'site_uuid' => $event->aggregateId,
			'level' => $event->level->value,
		];

		if ($dbid) {
			$this->db->update('sites_users', $data, ['dbid' => $dbid]);
		} else {
			$this->db->insert('sites_users', $data);
		}
	}
}
