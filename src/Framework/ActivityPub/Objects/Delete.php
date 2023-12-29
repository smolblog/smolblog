<?php

namespace Smolblog\Framework\ActivityPub\Objects;

/**
 * Indicates that the actor has deleted the object. If specified, the origin indicates the context from which the
 * object was deleted.
 */
readonly class Delete extends Activity {
	/**
	 * Get the object type.
	 *
	 * @return string
	 */
	public function type(): string {
		return 'Delete';
	}
}
