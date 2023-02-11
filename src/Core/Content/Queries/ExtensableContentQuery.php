<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Core\Content\ContentExtension;

/**
 * Denotes a query that fetches one or more content objects that should have extensions added.
 */
interface ExtensableContentQuery {
	/**
	 * Apply the given extension to the content.
	 *
	 * @param ContentExtension $extension Extension to apply.
	 * @return void
	 */
	public function setExtension(ContentExtension $extension): void;
}
