<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Schema\Schema;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Events\{ChannelAddedToSite, ChannelDeleted, ChannelSaved};
use Smolblog\Foundation\Service\Event\EventListenerService;
use Smolblog\Foundation\Service\Event\ProjectionListener;
use Smolblog\Foundation\Value\Fields\Identifier;

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
	 * @param DatabaseService $db Working database connection.
	 */
	public function __construct(private DatabaseService $db) {
	}

	/**
	 * Get a Channel.
	 *
	 * @param Identifier $channelId ID of the Channel.
	 * @return Channel|null
	 */
	public function channelById(Identifier $channelId): ?Channel {
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
		return is_string($result) ?
			Channel::fromJson($result) :
			Channel::deserializeValue($result); // @codeCoverageIgnore
	}

	/**
	 * Get all Channels linked to a particular Connection.
	 *
	 * @param Identifier $connectionId ID of Connection in question.
	 * @return Channel[]
	 */
	public function channelsForConnection(Identifier $connectionId): array {
		$query = $this->db->createQueryBuilder();
		$query
			->select('channel_obj')
			->from('channels')
			->where('connection_uuid = ?')
			->setParameter(0, $connectionId);
		$results = $query->fetchFirstColumn();

		return array_map(
			fn($res) => is_string($res) ? Channel::fromJson($res) : Channel::deserializeValue($res),
			$results,
		);
	}

	/**
	 * Get all Channels linked to a Site.
	 *
	 * @param Identifier $siteId ID of Site to check.
	 * @return Channel[]
	 */
	public function channelsForSite(Identifier $siteId): array {
		$query = $this->db->createQueryBuilder();
		$query->
			select('c.channel_obj')->
			from('channels', 'c')->
			innerJoin('c', 'channels_sites', 'cs', 'c.channel_uuid = cs.channel_uuid')->
			where('cs.site_uuid = ?')->
			setParameter(0, $siteId);
		$results = $query->fetchFirstColumn();

		return array_map(
			fn($res) => is_string($res) ? Channel::fromJson($res) : Channel::deserializeValue($res),
			$results,
		);
	}

	/**
	 * Check if a given site can push to a given channel.
	 *
	 * @param Identifier $siteId    ID of site in question.
	 * @param Identifier $channelId ID of channel in question.
	 * @return boolean
	 */
	public function siteCanUseChannel(Identifier $siteId, Identifier $channelId): bool {
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
			'channel_obj' => json_encode($event->channel),
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
				siteId: $event->aggregateId ?? Identifier::nil(),
				channelId: $event->entityId ?? Identifier::nil()
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
