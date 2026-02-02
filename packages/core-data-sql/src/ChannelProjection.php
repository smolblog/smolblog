<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\DomainEvent\EventListenerService;
use Cavatappi\Foundation\DomainEvent\ProjectionListener;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Infrastructure\Serialization\SerializationService;
use Doctrine\DBAL\Schema\Schema;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Events\{ChannelAddedToSite, ChannelDeleted, ChannelSaved};

/**
 * Store and retrieve Content objects.
 */
class ChannelProjection implements ChannelRepo, EventListenerService, DatabaseTableHandler {
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
		$channelTable = $schema->createTable($tableName('channels'));
		$channelTable->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
		$channelTable->addColumn('channel_uuid', 'guid');
		$channelTable->addColumn('connection_uuid', 'guid', ['notnull' => false]);
		$channelTable->addColumn('channel_obj', 'json');

		$channelTable->setPrimaryKey(['dbid']);
		$channelTable->addUniqueIndex(['channel_uuid']);
		$channelTable->addIndex(['connection_uuid']);

		$siteLinkTable = $schema->createTable($tableName('channels_sites'));
		$siteLinkTable->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
		$siteLinkTable->addColumn('channel_uuid', 'guid');
		$siteLinkTable->addColumn('site_uuid', 'guid');

		$siteLinkTable->setPrimaryKey(['dbid']);
		$siteLinkTable->addUniqueIndex(['channel_uuid', 'site_uuid']);

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
	 * Get a Channel.
	 *
	 * @param UuidInterface $channelId ID of the Channel.
	 * @return Channel|null
	 */
	public function channelById(UuidInterface $channelId): ?Channel {
		$query = $this->db->createQueryBuilder();
		$query
			->select('channel_obj')
			->from('channels')
			->where('channel_uuid = ?')->setParameter(0, $channelId);
		$result = $query->fetchOne();

		if ($result === false) {
			return null;
		}

		// This has to do with different DB engines which we cannot currently test.
		return is_string($result)
			? $this->serde->fromJson($result, as: Channel::class)
			: $this->serde->fromArray($result, as: Channel::class); // @codeCoverageIgnore
	}

	/**
	 * Get all Channels linked to a particular Connection.
	 *
	 * @param UuidInterface $connectionId ID of Connection in question.
	 * @return Channel[]
	 */
	public function channelsForConnection(UuidInterface $connectionId): array {
		$query = $this->db->createQueryBuilder();
		$query
			->select('channel_obj')
			->from('channels')
			->where('connection_uuid = ?')
			->setParameter(0, $connectionId);
		$results = $query->fetchFirstColumn();

		return array_map(
			fn($res) => is_string($res) ? $this->serde->fromJson($res, as: Channel::class) : $this->serde->fromArray($res, as: Channel::class),
			$results,
		);
	}

	/**
	 * Get all Channels linked to a Site.
	 *
	 * @param UuidInterface $siteId ID of Site to check.
	 * @return Channel[]
	 */
	public function channelsForSite(UuidInterface $siteId): array {
		$query = $this->db->createQueryBuilder();
		$query
			->select('c.channel_obj')
			->from('channels', 'c')
			->innerJoin('c', 'channels_sites', 'cs', 'c.channel_uuid = cs.channel_uuid')
			->where('cs.site_uuid = ?')
			->setParameter(0, $siteId);
		$results = $query->fetchFirstColumn();

		return array_map(
			fn($res) => is_string($res) ? $this->serde->fromJson($res, as: Channel::class) : $this->serde->fromArray($res, as: Channel::class),
			$results,
		);
	}

	/**
	 * Check if a given site can push to a given channel.
	 *
	 * @param UuidInterface $siteId    ID of site in question.
	 * @param UuidInterface $channelId ID of channel in question.
	 * @return boolean
	 */
	public function siteCanUseChannel(UuidInterface $siteId, UuidInterface $channelId): bool {
		$query = $this->db->createQueryBuilder();
		$query->select('1')
			->from('channels_sites')
			->where('site_uuid = ?')
			->andWhere('channel_uuid = ?')
			->setParameter(0, $siteId)
			->setParameter(1, $channelId);
		$result = $query->fetchOne();

		return $result ? true : false;
	}

	/**
	 * Saves a new Channel to the database
	 *
	 * @param ChannelSaved $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener]
	public function onChannelSaved(ChannelSaved $event): void {
		$checkQuery = $this->db->createQueryBuilder();
		$checkQuery
			->select('dbid')
			->from('channels')
			->where('channel_uuid = ?')
			->setParameter(0, $event->entityId);
		$dbid = $checkQuery->fetchOne();

		$data = [
			'channel_uuid' => $event->entityId,
			'connection_uuid' => $event->channel->connectionId,
			'channel_obj' => $this->serde->toJson($event->channel),
		];

		if ($dbid) {
			$this->db->update('channels', $data, ['dbid' => $dbid]);
		} else {
			$this->db->insert('channels', $data);
		}
	}

	/**
	 * Add the given channel to the given site.
	 *
	 * @param ChannelAddedToSite $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener]
	public function onChannelAddedToSite(ChannelAddedToSite $event): void {
		if (
			$this->siteCanUseChannel(
				siteId: $event->aggregateId ?? UuidFactory::nil(),
				channelId: $event->entityId ?? UuidFactory::nil(),
			)
		) {
			return;
		}

		$this->db->insert(
			'channels_sites',
			[
				'site_uuid' => $event->aggregateId,
				'channel_uuid' => $event->entityId,
			],
		);
	}

	/**
	 * Remove the given channel from the system.
	 *
	 * @param ChannelDeleted $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener]
	public function onChannelDeleted(ChannelDeleted $event): void {
		$this->db->delete('channels', ['channel_uuid' => $event->entityId]);
	}
}
