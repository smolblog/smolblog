<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Core\Content\ContentExtension;

/**
 * An event where a Content's extension has been edited.
 */
abstract class ContentExtensionEdited extends ContentEvent {
	/**
	 * Get the extension as of this event.
	 *
	 * @return ContentExtension
	 */
	abstract public function getNewExtension(): ContentExtension;
}
