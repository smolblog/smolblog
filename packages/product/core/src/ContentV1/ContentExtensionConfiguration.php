<?php

namespace Smolblog\Core\ContentV1;

use Smolblog\Framework\Objects\Value;

/**
 * Store configuration information for a Content Extension.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class ContentExtensionConfiguration extends Value {
	/**
	 * Construct the configuration.
	 *
	 * @param string $handle         Unique URL-friendly name for the content extension.
	 * @param string $displayName    Human-readable name for the content extension.
	 * @param string $extensionClass Fully-qualified class name of the content extension.
	 */
	public function __construct(
		public readonly string $handle,
		public readonly string $displayName,
		public readonly string $extensionClass,
	) {
	}
}
