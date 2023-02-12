<?php

namespace Smolblog\Core\Content;

use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Messages\StoppableMessageKit;

/**
 * Trait to handle authorization for content editing commands.
 *
 * The command must have userId, siteId, and contentId as properties.
 */
trait EditContentCommandKit {
	use StoppableMessageKit;

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
