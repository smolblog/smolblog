<?php

namespace Smolblog\Core\Content\Events;

/**
 * Indicates a content's title or body has changed.
 *
 * This should be implemented by any ContentEvents that result in a content's body changing.
 */
abstract class ContentBodyEdited extends ContentEvent {
	/**
	 * Get the updated HTML-formatted body text.
	 *
	 * @return array
	 */
	abstract public function getNewBody(): string;

	/**
	 * Get the updated title.
	 *
	 * @return array
	 */
	abstract public function getNewTitle(): string;
}
