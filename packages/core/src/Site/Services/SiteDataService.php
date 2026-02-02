<?php

namespace Smolblog\Core\Site\Services;

use Cavatappi\Foundation\Service;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Core\Site\Data\SiteRepo;
use Smolblog\Core\Site\Entities\Site;

/**
 * Retrieve Site data for use outside the domain model.
 *
 * Using this service is preferred to calling SiteRepo directly as this will enforce permissions and other logic.
 */
class SiteDataService implements Service {
	/**
	 * Construct the service.
	 *
	 * @param SiteRepo               $repo  Site object store.
	 * @param SitePermissionsService $perms Permission checks.
	 */
	public function __construct(private SiteRepo $repo, private SitePermissionsService $perms) {}

	/**
	 * Get detailed information on the given site. User must be able to manage settings on the site.
	 *
	 * @param UuidInterface $siteId Site to query.
	 * @param UuidInterface $userId User making the query.
	 * @return Site|null
	 */
	public function siteById(UuidInterface $siteId, UuidInterface $userId): ?Site {
		if (!$this->perms->canManageSettings(userId: $userId, siteId: $siteId)) {
			return null;
		}

		return $this->repo->siteById($siteId);
	}
}
