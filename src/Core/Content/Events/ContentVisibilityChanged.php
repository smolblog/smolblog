<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Core\Content\ContentVisibility;

/**
 * Indicates that a piece of content has changed visibility.
 *
 * I.E. "a thing was published" or "a thing was un-published."
 */
interface ContentVisibilityChanged {
	/**
	 * Get the updated ContentVisibility
	 *
	 * @return ContentVisibility
	 */
	public function newVisibility(): ContentVisibility;
}
