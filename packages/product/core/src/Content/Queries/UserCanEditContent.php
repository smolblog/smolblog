<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Query;

/**
 * Gives a truthy value if the given user can edit the given content.
 */
class UserCanEditContent extends Query {
	/**
	 * Construct the query.
	 *
	 * @param Identifier $userId    User to check.
	 * @param Identifier $contentId Content to check.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly Identifier $contentId,
	) {
		parent::__construct();
	}
}
