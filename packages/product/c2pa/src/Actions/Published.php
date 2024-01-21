<?php

namespace Smolblog\ContentProvenance\Actions;

use Smolblog\ContentProvenance\Action;

/**
 * Describes that the content was published (uploaded to Smolblog).
 */
class Published extends Action {
	/**
	 * Create the action.
	 *
	 * @param string|null $description Optional description.
	 */
	public function __construct(
		?string $description = null
	) {
		parent::__construct(type: 'c2pa.published', description: $description);
	}
}
