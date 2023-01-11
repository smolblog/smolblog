<?php

namespace Smolblog\Core\Content\Events;

/**
 * Indicates a piece of content has been posted to an external service.
 */
interface ContentSyndicated {
	/**
	 * Get the URL of the newly syndicated content.
	 *
	 * @return string
	 */
	public function newlySyndicatedUrl(): string;
}
