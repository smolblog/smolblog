<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Traits\{AuthorizableMessage, Memoizable, MemoizableKit};

/**
 * Get a given Content object as a full Content object.
 */
class ContentById extends Query implements Memoizable, AuthorizableMessage {
	use MemoizableKit;

	/**
	 * Create the query.
	 *
	 * If the content is unpublished (or has unpublished edits), a user ID will be required to view the unpublished
	 * info.
	 *
	 * @param Identifier      $id     ID for the content.
	 * @param Identifier|null $userId Optional user making the query.
	 */
	public function __construct(public Identifier $id, public ?Identifier $userId = null) {
		parent::__construct();
	}

	/**
	 * Check if the given user can see the given content.
	 *
	 * @return ContentVisibleToUser
	 */
	public function getAuthorizationQuery(): ContentVisibleToUser {
		return new ContentVisibleToUser(contentId: $this->id, userId: $this->userId);
	}
}
