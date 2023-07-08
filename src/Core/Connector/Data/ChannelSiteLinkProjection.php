<?php

namespace Smolblog\Core\Connector\Data;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\Connector\Entities\ChannelSiteLink;
use Smolblog\Core\Connector\Events\{ChannelSiteLinkSet};
use Smolblog\Core\Connector\Queries\ChannelsForSite;
use Smolblog\Core\Connector\Queries\SiteHasPermissionForChannel;
use Smolblog\Core\Connector\Queries\UserCanLinkChannelAndSite;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Messages\Projection;

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
}
