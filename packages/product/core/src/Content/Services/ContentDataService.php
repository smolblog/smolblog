<?php

namespace Smolblog\Core\Content\Services;

use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Foundation\Service;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Retrieve Content data for use outside the domain model.
 *
 * Using this service is preferred to calling ContentRepo directly as this will enforce permissions and other logic.
 */
class ContentDataService implements Service {
	/**
	 * Construct the service.
	 *
	 * @param SitePermissionsService $perms Check permissions.
	 * @param ContentRepo            $repo  Retrieve content objects.
	 */
	public function __construct(
		private SitePermissionsService $perms,
		private ContentRepo $repo,
	) {
	}

	/**
	 * Fetch the list of content available to this user.
	 *
	 * @param Identifier $siteId Site to retrieve content for.
	 * @param Identifier $userId User making the request.
	 * @return array
	 */
	public function contentList(Identifier $siteId, Identifier $userId): array {
		if ($this->perms->canEditAllContent(userId: $userId, siteId: $siteId)) {
			// Show all content.
			return $this->repo->contentList(forSite: $siteId);
		}

		// Show only this user's content.
		return $this->repo->contentList(forSite: $siteId, ownedByUser: $userId);
	}

	/**
	 * Get an individual piece of content.
	 *
	 * @param Identifier $contentId Content to retrieve.
	 * @param Identifier $userId    User making the request.
	 * @return Content|null Null if content does not exist or user does not have permission.
	 */
	public function contentById(Identifier $contentId, Identifier $userId): ?Content {
		$content = $this->repo->contentById($contentId);
		if (!isset($content)) {
			return null;
		}

		if (
			$content->userId == $userId ||
			$this->perms->canEditAllContent(userId: $userId, siteId: $content->siteId)
		) {
			return $content;
		}

		// Not permitted.
		return null;
	}
}
