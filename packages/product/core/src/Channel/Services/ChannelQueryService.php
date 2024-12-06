<?php

namespace Smolblog\Core\Channel\Services;

use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Foundation\Exceptions\ActionNotAuthorized;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Public service to query Channel data.
 *
 * Using this service is preferred to calling ChannelRepo directly since this service will check permissions for the
 * querying user.
 */
class ChannelQueryService {
	/**
	 * Construct the service
	 *
	 * @param ChannelRepo            $repo      Repository for Channel objects.
	 * @param SitePermissionsService $sitePerms Site Permissions.
	 */
	public function __construct(
		private ChannelRepo $repo,
		private SitePermissionsService $sitePerms,
	) {
	}

	/**
	 * Get the channels for the given site if the user is able to view them.
	 *
	 * @throws ActionNotAuthorized If the given user cannot push content for the given site.
	 *
	 * @param Identifier $siteId Site to get channels for.
	 * @param Identifier $userId User fetching the information.
	 * @return Channel[]
	 */
	public function channelsForSite(Identifier $siteId, Identifier $userId): array {
		if (!$this->sitePerms->canPushContent(userId: $userId, siteId: $siteId)) {
			throw new ActionNotAuthorized();
		}

		return $this->repo->channelsForSite($siteId);
	}
}
