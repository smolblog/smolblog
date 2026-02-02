<?php

namespace Smolblog\Core\Channel\Services;

use Cavatappi\Foundation\Exceptions\CodePathNotSupported;
use Cavatappi\Foundation\Service;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Permissions\GlobalPermissionsService;
use Smolblog\Core\Permissions\SitePermissionsService;

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
		private SitePermissionsService $sitePerms,
	) {}

	/**
	 * Get the channels the given site is assigned to.
	 *
	 * @param UuidInterface $siteId Site to get channels for.
	 * @param UuidInterface $userId User making the query.
	 * @return Channel[]
	 */
	public function channelsForSite(UuidInterface $siteId, UuidInterface $userId): array {
		if (
			!$this->sitePerms->canManageChannels(userId: $userId, siteId: $siteId)
			&& !$this->sitePerms->canPushContent(userId: $userId, siteId: $siteId)
		) {
			return [];
		}

		return $this->repo->channelsForSite($siteId);
	}

	/**
	 * Get the channels the user is able to assign.
	 *
	 * This will include channels enabled by the user's Connections.
	 *
	 * @throws CodePathNotSupported Method is not implemented yet.
	 * @codeCoverageIgnore
	 *
	 * @param UuidInterface $channelUserId User to get chnnels for.
	 * @param UuidInterface $userId        User making the query.
	 * @return array
	 */
	public function availableChannels(UuidInterface $channelUserId, UuidInterface $userId): array {
		if ($channelUserId != $userId && !$this->globalPerms->canManageOtherConnections($userId)) {
			return [];
		}

		throw new CodePathNotSupported(__METHOD__ . ' not yet implemented.');
		return [];
	}
}
