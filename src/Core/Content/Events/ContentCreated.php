<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Core\Content\BaseContent;

/**
 * Indicates an event where a new piece of Content has been created.
 */
interface ContentCreated {
	/**
	 * Get the Content object that has been created.
	 *
	 * @return BaseContent
	 */
	public function getContent(): BaseContent;
}
