<?php

namespace Smolblog\Core\Connector\Data;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\ChannelSiteLink;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Events\{ChannelSiteLinkSet};
use Smolblog\Core\Connector\Queries\ChannelsForAdmin;
use Smolblog\Core\Connector\Queries\ChannelsForSite;
use Smolblog\Core\Connector\Queries\SiteHasPermissionForChannel;
use Smolblog\Core\Connector\Queries\UserCanLinkChannelAndSite;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Messages\Projection;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Track permissions for sites and channels.
 */
class ChannelSiteLinkProjection implements Projection {
	public const TABLE = 'channel_site_links';

	/**
	 * Construct the projection.
	 *
	 * @param ConnectionInterface $db  Working database connection.
	 * @param MessageBus          $bus MessageBus for dispatching a security query.
	 */
	public function __construct(
		private ConnectionInterface $db,
		private MessageBus $bus,
	) {
	}

	/**
	 * Update a Channel and Site link.
	 *
	 * If no link exists, one is created with the given permissions. If the event has no permissions (both false), the
	 * link will be deleted.
	 *
	 * @param ChannelSiteLinkSet $event Event to persist.
	 * @return void
	 */
	public function onChannelSiteLinkSet(ChannelSiteLinkSet $event) {
		$linkId = ChannelSiteLink::buildId(channelId: $event->channelId, siteId: $event->siteId);

		if (!$event->canPull && !$event->canPush) {
			$this->db->table(self::TABLE)->where('link_uuid', '=', $linkId->toString())->delete();
			return;
		}

		$this->db->table(self::TABLE)->upsert(
			[
				'link_uuid' => $linkId->toString(),
				'channel_uuid' => $event->channelId->toString(),
				'site_uuid' => $event->siteId->toString(),
				'can_push' => $event->canPush,
				'can_pull' => $event->canPull,
			],
			'link_uuid',
			['can_push', 'can_pull']
		);
	}

	/**
	 * Get all Channels linked to a given site.
	 *
	 * @param ChannelsForSite $query Query to execute.
	 * @return void
	 */
	public function onChannelsForSite(ChannelsForSite $query) {
		$builder = $this->db->table(ChannelProjection::TABLE)->
			join(self::TABLE, self::TABLE . '.channel_uuid', '=', ChannelProjection::TABLE . '.channel_uuid')->
			where(self::TABLE . '.site_uuid', '=', $query->siteId->toString());

		if (isset($query->canPull)) {
			$builder = $builder->where(self::TABLE . '.can_pull', '=', $query->canPull);
		}
		if (isset($query->canPush)) {
			$builder = $builder->where(self::TABLE . '.can_push', '=', $query->canPush);
		}

		$results = $builder->select(ChannelProjection::TABLE . '.*')->get();

		$query->setResults($results->map(fn($row) => ChannelProjection::channelFromRow($row))->all());
	}

	/**
	 * Check if a site has permission to a given channel
	 *
	 * @param SiteHasPermissionForChannel $query Query to execute.
	 * @return void
	 */
	public function onSiteHasPermissionForChannel(SiteHasPermissionForChannel $query) {
		$linkId = ChannelSiteLink::buildId(channelId: $query->channelId, siteId: $query->siteId);
		$builder = $this->db->table(self::TABLE)->where('link_uuid', '=', $linkId->toString());

		if ($query->mustPull) {
			$builder = $builder->where('can_pull', '=', true);
		}
		if ($query->mustPush) {
			$builder = $builder->where('can_push', '=', true);
		}

		$query->setResults($builder->exists());
	}

	/**
	 * Check whether a user can link a channel and site.
	 *
	 * Currently the user must own the connection providing the channel and be an administrator on the site.
	 *
	 * Uses the UserHasPermissionForSite query.
	 *
	 * @param UserCanLinkChannelAndSite $query Query to execute.
	 * @return void
	 */
	public function onUserCanLinkChannelAndSite(UserCanLinkChannelAndSite $query) {
		$channelResults = $this->db->table(ChannelProjection::TABLE)->
			join(
				ConnectionProjection::TABLE,
				ChannelProjection::TABLE . '.connection_uuid',
				'=',
				ConnectionProjection::TABLE . '.connection_uuid'
			)->where(ConnectionProjection::TABLE . '.user_uuid', '=', $query->userId->toString())->
			where(ChannelProjection::TABLE . '.channel_uuid', '=', $query->channelId->toString())->
			exists();

		if (!$channelResults) {
			$query->setResults(false);
			return;
		}

		// This info isn't in the database yet.
		$query->setResults($this->bus->fetch(
			new UserHasPermissionForSite(siteId: $query->siteId, userId: $query->userId, mustBeAdmin: true)
		));
	}

	/**
	 * Handle the ChannelsForAdmin query.
	 *
	 * This is one of those queries that would be 2-3 queries normally, but here it's one trip to the database.
	 *
	 * @param ChannelsForAdmin $query Query to execute.
	 * @return void
	 */
	public function onChannelsForAdmin(ChannelsForAdmin $query) {
		$channelTable = ChannelProjection::TABLE;
		$connectionTable = ConnectionProjection::TABLE;

		$linksQuery = $this->db->table(self::TABLE)->where('site_uuid', '=', $query->siteId->toString());
		$results = $this->db->table($channelTable)->
			join($connectionTable, "$connectionTable.connection_uuid", '=', "$channelTable.connection_uuid")->
			leftJoinSub($linksQuery, 'links', "$channelTable.channel_uuid", '=', 'links.channel_uuid')->
			where("$connectionTable.user_uuid", '=', $query->userId->toString())->
			orWhereNotNull('links.link_uuid')->
			select(
				"$connectionTable.connection_uuid",
				"$connectionTable.user_uuid",
				"$connectionTable.provider",
				"$connectionTable.provider_key",
				"$connectionTable.display_name as connection_display_name",
				"$connectionTable.details as connection_details",
				"$channelTable.channel_uuid",
				"$channelTable.channel_key",
				"$channelTable.display_name as channel_display_name",
				"$channelTable.details as channel_details",
				"links.site_uuid",
				"links.can_pull",
				"links.can_push",
			)->get();

		$connections = [];
		$channels = [];
		$links = [];
		foreach ($results->all() as $row) {
			if (!array_key_exists($row->connection_uuid, $channels)) {
				$connections[$row->connection_uuid] = $this->adminConnectionFromRow($row);
				$channels[$row->connection_uuid] = [];
			}

			$channels[$row->connection_uuid][] = $this->adminChannelFromRow($row);

			if (isset($row->site_uuid)) {
				$links[$row->channel_uuid] = $this->adminLinkFromRow($row);
			}
		}

		$query->setResults([
			'connections' => $connections,
			'channels' => $channels,
			'links' => $links,
		]);
	}

	/**
	 * Create a Connection from the channels-for-admin query.
	 *
	 * @param object $row Database row.
	 * @return Connection
	 */
	private function adminConnectionFromRow(object $row): Connection {
		return new Connection(
			userId: Identifier::fromString($row->user_uuid),
			provider: $row->provider,
			providerKey: $row->provider_key,
			displayName: $row->connection_display_name,
			details: json_decode($row->connection_details, true),
		);
	}

	/**
	 * Create a Channel from the channels-for-admin query.
	 *
	 * @param object $row Database row.
	 * @return Channel
	 */
	private function adminChannelFromRow(object $row): Channel {
		return new Channel(
			connectionId: Identifier::fromString($row->connection_uuid),
			channelKey: $row->channel_key,
			displayName: $row->channel_display_name,
			details: json_decode($row->channel_details, true),
		);
	}

	/**
	 * Create a ChannelSiteLink from the channels-for-admin query.
	 *
	 * @param object $row Database row.
	 * @return ChannelSiteLink
	 */
	private function adminLinkFromRow(object $row): ChannelSiteLink {
		return new ChannelSiteLink(
			channelId: Identifier::fromString($row->channel_uuid),
			siteId: Identifier::fromString($row->site_uuid),
			canPull: $row->can_pull ?? false,
			canPush: $row->can_push ?? false,
		);
	}
}
