<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Query;

/**
 * Find if the given content is visible to the given user.
 *
 * Before content is fully public (in draft or scheduled), it cannot be viewed by the public or by users other than
 * administrators and its author. This query checks for those conditions.
 */
class ContentVisibleToUser extends Query {
	/**
	 * Construct the query.
	 *
	 * @param Identifier      $contentId ID of content being viewed.
	 * @param Identifier|null $userId    ID of user making the request; null if request is unauthenticated.
	 */
	public function __construct(
		public readonly Identifier $contentId,
		public readonly ?Identifier $userId,
	) {
		parent::__construct();
	}
}
