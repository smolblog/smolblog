<?php

namespace Smolblog\Core\Channel\Services;

use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Permissions\GlobalPermissionsService;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Service;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Retrieve Channel data for use outside the domain model.
 */
class ChannelDataService implements Service {
	/**
	 * Construct the service
	 *
	 * @param ChannelRepo              $repo        Channels data store.
	 * @param GlobalPermissionsService $globalPerms Global permissions.
	 * @param SitePermissionsService   $sitePerms   Site-specific permissions.
	 */
	public function __construct(
		private ChannelRepo $repo,
		private GlobalPermissionsService $globalPerms,
		private SitePermissionsService $sitePerms
	) {
	}

	/**
	 * Get the channels the given site is assigned to.
	 *
	 * @param Identifier $siteId Site to get channels for.
	 * @param Identifier $userId User making the query.
	 * @return array
	 */
	public function channelsForSite(Identifier $siteId, Identifier $userId): array {
		if (!$this->sitePerms->canManageChannels(userId: $userId, siteId: $siteId)) {
			return [];
		}

		return $this->repo->channelsForSite($siteId);
	}

	/**
	 * Get the channels the user is able to assign.
	 *
	 * This will include channels enabled by the user's Connections.
	 *
	 * @param Identifier $channelUserId User to get chnnels for.
	 * @param Identifier $userId        User making the query.
	 * @return array
	 */
	public function availableChannels(Identifier $channelUserId, Identifier $userId): array {
		if ($channelUserId != $userId && !$this->globalPerms->canManageOtherConnections($userId)) {
			return [];
		}

		throw new CodePathNotSupported(__METHOD__ . ' not yet implemented.');
		return [];
	}
}
