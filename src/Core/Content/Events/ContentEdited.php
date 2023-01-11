<?php

namespace Smolblog\Core\Content\Events;

/**
 * Indicates a piece of content has been edited and what changes have been made.
 *
 * Note that visibility and syndication have their own events as they are either more significant (visibility) or more
 * common (syndication).
 */
interface ContentEdited {
	/**
	 * Get the updated fields as an associative array.
	 *
	 * @return array
	 */
	public function getChanges(): array;
}
