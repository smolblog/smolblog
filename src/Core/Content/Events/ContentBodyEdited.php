<?php

namespace Smolblog\Core\Content\Events;

/**
 * Indicates a content's title or body has changed.
 *
 * This should be implemented by any ContentEvents that result in a content's body changing.
 */
interface ContentBodyEdited {
	/**
	 * Get the updated HTML-formatted body text.
	 *
	 * @return array
	 */
	public function getNewBody(): string;

	/**
	 * Get the updated title.
	 *
	 * @return array
	 */
	public function getNewTitle(): string;
}
