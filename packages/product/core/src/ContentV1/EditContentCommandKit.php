<?php

namespace Smolblog\Core\ContentV1;

use Smolblog\Core\ContentV1\Queries\UserCanEditContent;
use Smolblog\Framework\Messages\Query;

/**
 * Trait to handle authorization for content editing commands.
 *
 * The command must have userId, siteId, and contentId as properties.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
trait EditContentCommandKit {
	/**
	 * The user must be able to edit the content.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserCanEditContent(
			userId: $this->userId,
			siteId: $this->siteId,
			contentId: $this->contentId,
		);
	}
}
