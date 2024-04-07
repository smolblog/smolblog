<?php

namespace Smolblog\Core\ContentV1\Events;

/**
 * Indicates a content's title or body has changed.
 *
 * This should be implemented by any ContentEvents that result in a content's body changing.
 */
abstract class ContentBodyEdited extends ContentEvent {
	/**
	 * Get the updated HTML-formatted body text. Null if no change.
	 *
	 * @return string
	 */
	abstract public function getNewBody(): ?string;

	/**
	 * Get the updated title. Null if no change.
	 *
	 * @return string
	 */
	abstract public function getNewTitle(): ?string;
}
