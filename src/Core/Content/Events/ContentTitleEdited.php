<?php

namespace Smolblog\Core\Content\Events;

/**
 * Indicates a content's title has changed.
 *
 * This should be implemented by any ContentEvents that result in a content's title changing.
 */
interface ContentTitleEdited {
	/**
	 * Get the updated title.
	 *
	 * @return array
	 */
	public function getNewTitle(): string;
}
