<?php

namespace Smolblog\Framework\ActivityPub\Objects;

/**
 * Indicates that the actor accepts the object. The target property can be used in certain circumstances to indicate
 * the context into which the object has been accepted.
 */
readonly class Accept extends Activity {
	/**
	 * Get the object type.
	 *
	 * @return string
	 */
	public function type(): string {
		return 'Accept';
	}
}
