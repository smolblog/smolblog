<?php

namespace Smolblog\Core\ContentV1\Events;

use Smolblog\Core\ContentV1\ContentExtension;

/**
 * An event where a Content's extension has been edited.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
abstract class ContentExtensionEdited extends ContentEvent {
	/**
	 * Get the extension as of this event.
	 *
	 * @return ContentExtension
	 */
	abstract public function getNewExtension(): ContentExtension;
}
