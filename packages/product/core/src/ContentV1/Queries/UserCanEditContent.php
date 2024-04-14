<?php

namespace Smolblog\Core\ContentV1\Queries;

use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Gives a truthy value if the given user can edit the given content on the given site.
 */
readonly class UserCanEditContent extends Query {
	/**
	 * Construct the query.
	 *
	 * @param Identifier $userId    User to check.
	 * @param Identifier $siteId    Site to check.
	 * @param Identifier $contentId Content to check.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly Identifier $contentId,
	) {
	}
}
