<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Core\Content\ContentExtension;

trait ExtensableContentQueryKit {
	/**
	 * Apply the given extension to the content.
	 *
	 * @param ContentExtension $extension Extension to apply.
	 * @return void
	 */
	public function setExtension(ContentExtension $extension): void {
		$this->results->attachExtension($extension);
	}
}
